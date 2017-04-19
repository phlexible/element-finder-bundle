<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterManager;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\QueryEnhancerInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Matcher\TreeNodeMatcherInterface;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;

/**
 * Element finder.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FindExecutor implements ExecutorInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var TreeNodeMatcherInterface
     */
    private $treeNodeMatcher;

    /**
     * @var array
     */
    private $tidSkipList = array();

    /**
     * @param Connection               $connection
     * @param FilterManager            $filterManager
     * @param TreeNodeMatcherInterface $treeNodeMatcher
     */
    public function __construct(
        Connection $connection,
        FilterManager $filterManager,
        TreeNodeMatcherInterface $treeNodeMatcher
    ) {
        $this->connection = $connection;
        $this->filterManager = $filterManager;
        $this->treeNodeMatcher = $treeNodeMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ExecutionDescriptor $descriptor)
    {
        $filters = array();
        foreach ($descriptor->getConfig()->getFilters() as $filter) {
            $filters[$filter] = $this->filterManager->get($filter);
        }

        $matchedTreeIds = array();
        if ($descriptor->getConfig()->getTreeId()) {
            $matchedTreeIds = $this->treeNodeMatcher->getMatchingTreeIdsByLanguage(
                $descriptor->getConfig()->getTreeId(),
                $descriptor->getConfig()->getMaxDepth(),
                $descriptor->isPreview(),
                $descriptor->getLanguages()
            );
        }

        $results = array();
        $query = '';
        if ($matchedTreeIds !== null) {
            $qb = $this->createSelect(
                $descriptor->getConfig(),
                $descriptor->isPreview(),
                $descriptor->getLanguages(),
                $matchedTreeIds,
                $filters
            );
            $statement = $qb->execute();
            while ($item = $statement->fetch()) {
                $results[$item['tree_id']] = $item;
            }
            $query = $qb->getSQL();
        }

        if (!$descriptor->getConfig()->getSortField() && $matchedTreeIds) {
            // sort by tree order
            $treeIds = $this->treeNodeMatcher->flatten($matchedTreeIds);
            foreach ($treeIds as $index => $treeId) {
                if (array_key_exists($treeId, $results)) {
                    $results[$treeId]['sort_field'] = $index;
                }
            }
        }

        $results = $this->sortResults($results, $descriptor->getConfig()->getSortDir());

        return new ExecutionResult($descriptor, $filters, $results, $query);
    }

    /**
     * @param array  $results
     * @param string $sortDir
     *
     * @return array
     */
    private function sortResults(array $results, $sortDir)
    {
        $sortedColumn = array_column($results, 'sort_field', 'tree_id');
        $sortedColumn = array_map(
            function ($str) {
                return mb_strtoupper($str, 'UTF-8');
            },
            $sortedColumn
        );

        if ($sortDir === 'DESC') {
            arsort($sortedColumn, SORT_NATURAL);
        } else {
            asort($sortedColumn, SORT_NATURAL);
        }

        $orderedResult = array();
        foreach (array_keys($sortedColumn) as $key) {
            $orderedResult[] = $results[$key];
        }

        return $orderedResult;
    }

    /**
     * Apply filter and limit clause.
     *
     * @param ElementFinderConfig $config
     * @param bool                $isPreview
     * @param array               $languages
     * @param array|null          $matchedTreeIds
     * @param array               $filters
     *
     * @return QueryBuilder
     */
    private function createSelect(
        ElementFinderConfig $config,
        $isPreview,
        array $languages,
        array $matchedTreeIds = array(),
        array $filters = array()
    )
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select(
                array(
                    'lookup.tree_id',
                    'lookup.eid',
                    'lookup.version',
                    'lookup.elementtype_id',
                    'lookup.is_preview',
                    'lookup.in_navigation',
                    'lookup.is_restricted',
                    'lookup.published_at',
                    'lookup.custom_date',
                    'lookup.language',
                    //'lookup.online_version',
                )
            )
            ->from('elementfinder_lookup_element', 'lookup');

        if (count($matchedTreeIds)) {
            $or = $qb->expr()->orX();
            foreach ($matchedTreeIds as $language => $tids) {
                $or->add(
                    $qb->expr()->andX(
                        $qb->expr()->in('lookup.tree_id', $tids),
                        $qb->expr()->in('lookup.language', ':languages')
                    )
                );
            }
            $qb->where($or);
            $qb->setParameter('languages', $languages, Connection::PARAM_STR_ARRAY);
        }

        if ($isPreview) {
            $qb->andWhere($qb->expr()->eq('lookup.is_preview', 1));
        } else {
            $qb->andWhere($qb->expr()->eq('lookup.is_preview', 0));
        }

        if ($config->getMetaField() && $config->getMetaKeywords()) {
            $metaI = 0;
            foreach ($config->getMetaKeywords() as $key => $value) {
                $alias = 'meta' . ++$metaI;
                $qb
                    ->join(
                        'lookup',
                        'elementfinder_lookup_meta',
                        $alias,
                        $qb->expr()->andX(
                            $qb->expr()->eq("$alias.eid", 'lookup.eid'),
                            $qb->expr()->eq("$alias.version", 'lookup.version'),
                            $qb->expr()->eq("$alias.language", 'lookup.language')
                        )
                    )
                    ->andWhere($qb->expr()->eq("$alias.field", $qb->expr()->literal($config->getMetaField())));

                $multiValueSelects = array();
                foreach (explode(',', $value) as $singleValue) {
                    $singleValue = trim($singleValue);
                    $multiValueSelects[] = $qb->expr()->eq(
                        "$alias.value",
                        $qb->expr()->literal(mb_strtolower(html_entity_decode($singleValue, ENT_COMPAT, 'UTF-8')))
                    );
                }

                if (count($multiValueSelects)) {
                    $qb->andWhere(implode(' OR ', $multiValueSelects));
                }
            }
        }

        if (count($config->getElementtypeIds())) {
            $elementtypeIds = $config->getElementtypeIds();
            foreach ($elementtypeIds as $index => $elementtypeId) {
                $elementtypeIds[$index] = $qb->expr()->literal($elementtypeId);
            }
            $qb->andWhere($qb->expr()->in('lookup.elementtype_id', $elementtypeIds));
        }

        if ($config->inNavigation()) {
            $qb->andWhere('lookup.in_navigation = 1');
        }

        if (count($this->tidSkipList)) {
            $tidSkipList = $this->tidSkipList;

            $qb->andWhere($qb->expr()->notIn('lookup.tree_id', $tidSkipList));
        }

        /*
        if ($country) {
            if ($country !== 'global') {
                $qb->andWhere(
                    '(lookup.tree_id IN (SELECT DISTINCT tid FROM element_tree_context WHERE context = ? OR context = "global") OR lookup.tree_id NOT IN (SELECT DISTINCT tid from element_tree_context))',
                    $country
                );
            } else {
                $qb->andWhere(
                    '(lookup.tree_id IN (SELECT DISTINCT tid FROM element_tree_context WHERE context = "global") OR lookup.tree_id NOT IN (SELECT DISTINCT tid from element_tree_context))'
                );
            }
        }
        */

        $qb->groupBy('lookup.eid');

        // apply filters
        foreach ($filters as $filter) {
            if ($filter && $filter instanceof QueryEnhancerInterface) {
                $filter->enhanceQuery($config, $qb);
            }
        }

        // set sort information
        $this->applySort($qb, $config, $isPreview);

        return $qb;
    }

    /**
     * Add a sort criteria to the select statement.
     *
     * @param QueryBuilder        $qb
     * @param ElementFinderConfig $config
     * @param bool                $isPreview
     */
    private function applySort(QueryBuilder $qb, ElementFinderConfig $config, $isPreview)
    {
        $sortField = $config->getSortField();
        if (!$sortField) {
            return;
        }

        if (ElementFinderConfig::SORT_TITLE_BACKEND === $sortField) {
            $this->applySortByTitle($qb, 'backend', $config);
        } elseif (ElementFinderConfig::SORT_TITLE_PAGE === $sortField) {
            $this->applySortByTitle($qb, 'page', $config);
        } elseif (ElementFinderConfig::SORT_TITLE_NAVIGATION === $sortField) {
            $this->applySortByTitle($qb, 'navigation', $config);
        } elseif (ElementFinderConfig::SORT_PUBLISH_DATE === $sortField) {
            $this->applySortByPublishDate($qb, $config, $isPreview);
        } elseif (ElementFinderConfig::SORT_CUSTOM_DATE === $sortField) {
            $this->applySortByCustomDate($qb, $config);
        } else {
            $this->applySortByField($qb, $config);
        }
    }

    /**
     * Add field sorting to select statement.
     *
     * @param QueryBuilder        $qb
     * @param ElementFinderConfig $config
     */
    private function applySortByField(QueryBuilder $qb, ElementFinderConfig $config)
    {
        $qb
            ->addSelect('sort_esv.content AS sort_field')
            ->join(
                'lookup',
                'element_structure',
                'sort_es',
                'lookup.eid = sort_es.eid AND lookup.version = sort_es.version'
            )
            ->join(
                'sort_d',
                'element_structure_value',
                'sort_esv',
                'sort_es.data_id = sort_esvl.data_id AND sort_es.version = sort_esv.version AND sort_es.eid = sort_esv.eid AND sort_es.ds_id = ' . $qb->expr(
                )->literal($config->getSortField()) . ' AND sort_esv.language = lookup.language'
            );
    }

    /**
     * Add title sorting to select statement.
     *
     * @param QueryBuilder        $qb
     * @param string              $title
     * @param ElementFinderConfig $config
     */
    private function applySortByTitle(QueryBuilder $qb, $title, ElementFinderConfig $config)
    {
        $qb
            ->addSelect("sort.$title AS sort_field")
            ->leftJoin(
                'lookup',
                'element_version',
                'sort_ev',
                'lookup.eid = sort_ev.eid AND lookup.version = sort_ev.version'
            )
            ->leftJoin(
                'sort_ev',
                'element_version_mapped_field',
                'sort',
                'sort_ev.id = sort.element_version_id AND lookup.language = sort.language'
            );
    }

    /**
     * Add title sorting to select statement.
     *
     * @param QueryBuilder        $qb
     * @param ElementFinderConfig $config
     * @param bool                $isPreview
     */
    private function applySortByPublishDate(QueryBuilder $qb, ElementFinderConfig $config, $isPreview)
    {
        $qb
            ->addSelect('lookup.published_at AS sort_field');
    }

    /**
     * Add title sorting to select statement.
     *
     * @param QueryBuilder        $qb
     * @param ElementFinderConfig $config
     */
    private function applySortByCustomDate(QueryBuilder $qb, ElementFinderConfig $config)
    {
        $qb
            ->addSelect('lookup.custom_date AS sort_field');
    }
}
