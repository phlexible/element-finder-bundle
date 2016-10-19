<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\DataCollector;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\DebugElementFinder;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinder;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

/**
 * Data collector for element finder result pools
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var DebugElementFinder
     */
    private $elementFinder;

    /**
     * @param null|ElementFinder $elementFinder
     */
    public function __construct($elementFinder = null)
    {
        if (null !== $elementFinder && $elementFinder instanceof ElementFinder) {
            $this->elementFinder = $elementFinder;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // everything is done as late as possible
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        if (null !== $this->elementFinder) {
            $this->data['updated_count'] = $this->elementFinder->countUpdatedResultPools();
            $this->data['cached_count'] = $this->elementFinder->countCachedResultPools();
            $this->data['total_count'] = $this->data['updated_count'] + $this->data['cached_count'];

            $updatedResultPools = array();
            $cachedResultPools = array();

            foreach ($this->elementFinder->getUpdatedResultPools() as $resultPool) {
                $updatedResultPools[] = $this->poolToArray($resultPool);
            }
            foreach ($this->elementFinder->getCachedResultPools() as $resultPool) {
                $cachedResultPools[] = $this->poolToArray($resultPool);
            }

            $this->data['updated_result_pools'] = $updatedResultPools;
            $this->data['cached_result_pools'] = $cachedResultPools;
        }
    }

    /**
     * @param ResultPool $resultPool
     *
     * @return array
     */
    private function poolToArray(ResultPool $resultPool)
    {
        $config = $resultPool->getConfig();
        return array(
            'config' => array(
                'elementtype_ids' => $config->getElementtypeIds(),
                'filter' => $config->getFilter(),
                'max_depth' => $config->getMaxDepth(),
                'meta_field' => $config->getMetaField(),
                'meta_keywords' => $config->getMetaKeywords(),
                'page_size' => $config->getPageSize(),
                'sort_field' => $config->getSortField(),
                'sort_dir' => $config->getSortDir(),
                'template' => $config->getTemplate(),
                'tree_id' => $config->getTreeId(),
            ),
            'parameters' => $resultPool->getParameters(),
            'filters' => $resultPool->getFilters(),
            #'count' => count($resultPool),
            #'facets' => $resultPool->getFacets(),
            'raw_facets' => $resultPool->getRawFacets(),
            'facet_names' => $resultPool->getFacetNames(),
            'created_at' => $resultPool->getCreatedAt(),
            'identifier' => $resultPool->getIdentifier(),
            'languages' => $resultPool->getLanguages(),
            'query' => $resultPool->getQuery(),
        );
    }

    /**
     * Gets the called events.
     *
     * @return int
     */
    public function countAll()
    {
        return isset($this->data['total_count']) ? $this->data['total_count'] : 0;
    }

    /**
     * Gets the number of updated result pools events.
     *
     * @return int
     */
    public function countUpdated()
    {
        return isset($this->data['updated_count']) ? $this->data['updated_count'] : 0;
    }

    /**
     * Gets the number of cached result pools.
     *
     * @return int
     */
    public function countCached()
    {
        return isset($this->data['cached_count']) ? $this->data['cached_count'] : 0;
    }

    /**
     * Gets the updated result pools.
     *
     * @return array
     */
    public function getUpdatedResultPools()
    {
        return isset($this->data['updated_result_pools']) ? $this->data['updated_result_pools'] : [];
    }

    /**
     * Gets the updated result pools.
     *
     * @return array
     */
    public function getCachedResultPools()
    {
        return isset($this->data['cached_result_pools']) ? $this->data['cached_result_pools'] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'element_finder';
    }
}
