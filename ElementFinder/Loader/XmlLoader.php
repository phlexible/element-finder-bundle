<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Loader;

use FluentDOM\Document;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterManager;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultItem;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;

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

        $configAttributes = $xml->config->attributes();
        $config = new ElementFinderConfig();
        $config
            ->setTreeId((int) $configAttributes['treeId'])
            ->setElementtypeIds(explode(',', (string) $configAttributes['elementtypeIds']))
            ->setMaxDepth((int) $configAttributes['maxDepth'])
            ->setMetaField((string) $configAttributes['metaField'])
            ->setMetaKeywords(json_decode((string) $configAttributes['metaKeywords'], true))
            ->setNavigation((bool) $configAttributes['navigation'])
            ->setSortField((string) $configAttributes['sortField'])
            ->setSortDir((string) $configAttributes['sortDir'])
            ->setTemplate((string) $configAttributes['template']);

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