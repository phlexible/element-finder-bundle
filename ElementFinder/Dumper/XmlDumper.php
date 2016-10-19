<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Dumper;

use FluentDOM\Document;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;

/**
 * Xml result pool dumper
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

        $languagesNode = $root->appendElement('languages');
        foreach ($pool->getLanguages() as $language) {
            $languagesNode->appendElement('language', $language);
        }

        $configNode = $root->appendElement('config');
        $configNode->appendElement('value', $pool->getConfig()->getTreeId(), array('key' => 'treeId'));
        $configNode->appendElement('value', implode(',', $pool->getConfig()->getElementtypeIds()), array('key' => 'elementtypeIds'));
        $configNode->appendElement('value', $pool->getConfig()->getMaxDepth(), array('key' => 'maxDepth'));
        $configNode->appendElement('value', $pool->getConfig()->getMetaField(), array('key' => 'metaField'));
        $configNode->appendElement('value', $pool->getConfig()->getMetaKeywords() ? json_encode($pool->getConfig()->getMetaKeywords()) : '', array('key' => 'metaKeywords'));
        $configNode->appendElement('value', $pool->getConfig()->inNavigation() ? 1 : 0, array('key' => 'navigation'));
        $configNode->appendElement('value', $pool->getConfig()->getSortField(), array('key' => 'sortField'));
        $configNode->appendElement('value', $pool->getConfig()->getSortDir(), array('key' => 'sortDir'));
        $configNode->appendElement('value', $pool->getConfig()->getTemplate(), array('key' => 'template'));
        $configNode->appendElement('value', $pool->getConfig()->getPageSize(), array('key' => 'pageSize'));

        $filtersNode = $root->appendElement('filters');
        foreach ($pool->getFilters() as $filterName => $filter) {
            $filtersNode->appendElement('filter', $filterName);
        }

        $itemsNode = $root->appendElement('items');
        foreach ($pool->rawAll() as $item) {
            $attributes = array(
                'treeId'        => $item->getTreeId(),
                'eid'           => $item->getEid(),
                'version'       => $item->getVersion(),
                'language'      => $item->getLanguage(),
                'elementtypeId' => $item->getElementtypeId(),
                'isPreview'     => $item->isPreview() ? 1 : 0,
                'inNavigation'  => $item->isInNavigation() ? 1 : 0,
                'isRestricted'  => $item->isRestricted() ? 1 : 0,
                'customDate'    => $item->getCustomDate() ? $item->getCustomDate()->format('Y-m-d H:i:s') : '',
                'publishedAt'   => $item->getPublishedAt() ? $item->getPublishedAt()->format('Y-m-d H:i:s') : '',
                'sortField'     => $item->getSortField(),
            );
            $itemNode = $itemsNode->appendElement('item', '', $attributes);

            foreach ($item->getExtras() as $key => $value) {
                $itemNode->appendElement('extra', null, array('key' => $key))->appendChild($dom->createCDATASection($value));
            }
        }

        return $dom->saveXML();
    }
}
