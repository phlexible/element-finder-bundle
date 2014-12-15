<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Loader;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterManager;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultItem;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;

/**
 * Xml loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(FilterManager $filterManager, $filename)
    {
        $xml = simplexml_load_file($filename);

        $rootAttributes = $xml->attributes();

        $identifier = (string) $rootAttributes['identifier'];
        $query = (string) $rootAttributes['query'];
        $createdAt = new \DateTIme((string) $rootAttributes['createdAt']);


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
                $extra[(string) $extraAttributes['key']] = (string) $extraNode;
            }

            $items[] = new ResultItem(
                (int) $itemAttributes['treeId'],
                (int) $itemAttributes['eid'],
                (int) $itemAttributes['version'],
                (string) $itemAttributes['language'],
                (string) $itemAttributes['elementtypeId'],
                (bool) $itemAttributes['isPreview'],
                (bool) $itemAttributes['inNavigation'],
                (bool) $itemAttributes['isRestricted'],
                (string) $itemAttributes['publishedAt'],
                (string) $itemAttributes['customDate'],
                (string) $itemAttributes['sortField'],
                $extra
            );
        }

        $filters = array();
        foreach ($xml->filters->filter as $filterNode) {
            $filterName = (string) $filterNode;
            $filter = $filterManager->get($filterName);
            $filters[] = $filter;
        }

        $result = new ResultPool($identifier, $config, $query, $items, $filters, $createdAt);

        return $result;
    }
}