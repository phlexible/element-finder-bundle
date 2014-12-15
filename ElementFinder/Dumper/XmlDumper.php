<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Dumper;

use FluentDOM\Document;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;

/**
 * Xml dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlDumper implements DumperInterface
{
    /**
     * {@inheritdoc}
     */
    public function dump(ResultPool $pool)
    {
        $dom = new Document();
        $dom->formatOutput = true;
        $root = $dom->appendElement('pool', array('identifier' => $pool->getIdentifier(), 'createdAt' => $pool->getCreatedAt()->format('Y-m-d H:i:s')));

        $root->appendElement('query', $pool->getQuery());

        $configNode = $root->appendElement('config');
        $configNode->appendElement('value', '', array('treeId' => $pool->getConfig()->getTreeId()));
        $configNode->appendElement('value', '', array('elementtypeIds' => implode(',', $pool->getConfig()->getElementtypeIds())));
        $configNode->appendElement('value', '', array('maxDepth' => $pool->getConfig()->getMaxDepth()));
        $configNode->appendElement('value', '', array('metaField' => $pool->getConfig()->getMetaField()));
        $configNode->appendElement('value', '', array('metaKeywords' => $pool->getConfig()->getMetaKeywords() ? json_encode($pool->getConfig()->getMetaKeywords()) : ''));
        $configNode->appendElement('value', '', array('navigation' => $pool->getConfig()->inNavigation() ? 1 : 0));
        $configNode->appendElement('value', '', array('sortField' => $pool->getConfig()->getSortField()));
        $configNode->appendElement('value', '', array('sortDir' => $pool->getConfig()->getSortDir()));
        $configNode->appendElement('value', '', array('template' => $pool->getConfig()->getTemplate()));

        $filtersNode = $root->appendElement('filters');
        foreach ($pool->getFilters() as $filter) {
            $filtersNode->appendElement('filter', get_class($filter));
        }

        $itemsNode = $root->appendElement('items');
        foreach ($pool->all() as $item) {
            $attributes = array(
                'treeId'        => $item->getTreeId(),
                'eid'           => $item->getEid(),
                'version'       => $item->getVersion(),
                'language'      => $item->getLanguage(),
                'elementtypeId' => $item->getElementtypeId(),
                'isPreview'     => $item->isPreview() ? 1 : 0,
                'inNavigation'  => $item->isInNavigation() ? 1 : 0,
                'isRestricted'  => $item->isIsRestricted() ? 1 : 0,
                'customDate'    => $item->getCustomDate() ? $item->getCustomDate()->format('Y-m-d H:i:s') : '',
                'publishedAt'   => $item->getPublishedAt() ? $item->getPublishedAt()->format('Y-m-d H:i:s') : '',
                'sortField'     => $item->getSortField(),
            );
            $itemNode = $itemsNode->appendElement('item', '', $attributes);

            foreach ($item->getExtras() as $key => $value) {
                $itemNode->appendElement('extra', $value, array('key' => $key));
            }
        }

        return $dom->saveXML();
    }
}