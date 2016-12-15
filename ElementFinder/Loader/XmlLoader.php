<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Loader;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterManager;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItem;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;

/**
 * Xml loader.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlLoader implements LoaderInterface
{
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @param FilterManager $filterManager
     */
    public function __construct(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load($filename)
    {
        $xml = simplexml_load_file($filename);

        $rootAttributes = $xml->attributes();

        $identifier = (string) $rootAttributes['identifier'];
        $query = (string) $xml->query;
        $createdAt = new \DateTime((string) $rootAttributes['createdAt']);

        $languages = array();
        foreach ($xml->languages->language as $languageNode) {
            $languages[] = (string) $languageNode;
        }

        $config = new ElementFinderConfig();
        foreach ($xml->config->value as $valueNode) {
            $valueAttributes = $valueNode->attributes();
            if (!strlen((string) $valueNode)) {
                continue;
            }
            if ((string) $valueAttributes['key'] === 'treeId') {
                $config->setTreeId((int) $valueNode);
            } elseif ((string) $valueAttributes['key'] === 'elementtypeIds') {
                $config->setElementtypeIds(explode(',', (string) $valueNode));
            } elseif ((string) $valueAttributes['key'] === 'maxDepth') {
                $config->setMaxDepth((string) $valueNode);
            } elseif ((string) $valueAttributes['key'] === 'metaField') {
                $config->setMetaField((string) $valueNode);
            } elseif ((string) $valueAttributes['key'] === 'metaKeywords') {
                $config->setMetaKeywords(json_decode((string) $valueNode, true));
            } elseif ((string) $valueAttributes['key'] === 'navigation') {
                $config->setNavigation((bool) $valueNode);
            } elseif ((string) $valueAttributes['key'] === 'sortField') {
                $config->setSortField((string) $valueNode);
            } elseif ((string) $valueAttributes['key'] === 'sortDir') {
                $config->setSortDir((string) $valueNode);
            } elseif ((string) $valueAttributes['key'] === 'template') {
                $config->setTemplate((string) $valueNode);
            } elseif ((string) $valueAttributes['key'] === 'pageSize') {
                $config->setPageSize((int) $valueNode);
            }
        }

        $items = array();
        foreach ($xml->items->item as $itemNode) {
            $itemAttributes = $itemNode->attributes();

            $extra = array();
            foreach ($itemNode->extra as $extraNode) {
                $extraAttributes = $extraNode->attributes();
                $extraKey = (string) $extraAttributes['key'];
                $extraType = (string) $extraAttributes['type'];
                switch ($extraType) {
                    case 'float':
                    case 'double':
                        $extraValue = (float) $extraNode;
                        break;
                    case 'integer':
                        $extraValue = (int) $extraNode;
                        break;
                    case 'boolean':
                        $extraValue = (bool) $extraNode;
                        break;
                    case 'array':
                        $extraValue = json_decode((string) $extraNode, true);
                        break;
                    case 'string':
                    default:
                        $extraValue = (string) $extraNode;
                }
                $extra[$extraKey] = $extraValue;
            }

            $items[] = new ResultItem(
                (int) $itemAttributes['treeId'],
                (int) $itemAttributes['eid'],
                (int) $itemAttributes['version'],
                (string) $itemAttributes['language'],
                (string) $itemAttributes['elementtypeId'],
                (bool) (string) $itemAttributes['isPreview'],
                (bool) (string) $itemAttributes['inNavigation'],
                (bool) (string) $itemAttributes['isRestricted'],
                (string) $itemAttributes['publishedAt'] ? new \DateTimeImmutable((string) $itemAttributes['publishedAt']) : null,
                (string) $itemAttributes['customDate'] ? new \DateTimeImmutable((string) $itemAttributes['customDate']) : null,
                (string) $itemAttributes['sortField'],
                $extra
            );
        }

        $filters = array();
        foreach ($xml->filters->filter as $filterNode) {
            $filterName = (string) $filterNode;
            $filter = $this->filterManager->get($filterName);
            $filters[] = $filter;
        }

        $result = new ResultPool($identifier, $config, $languages, $query, $items, $filters, $createdAt);

        return $result;
    }
}
