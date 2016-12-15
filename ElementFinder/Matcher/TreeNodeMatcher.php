<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Matcher;

use Exception;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;

/**
 * Tree node matcher.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeNodeMatcher implements TreeNodeMatcherInterface
{
    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $elementHistoryManager;

    /**
     * @var bool
     */
    private $useElementLanguageAsFallback;

    /**
     * @param TreeManager                    $treeManager
     * @param ElementService                 $elementService
     * @param ElementHistoryManagerInterface $elementHistoryManager
     * @param bool                           $useElementLanguageAsFallback
     */
    public function __construct(
        TreeManager $treeManager,
        ElementService $elementService,
        ElementHistoryManagerInterface $elementHistoryManager,
        $useElementLanguageAsFallback)
    {
        $this->treeManager = $treeManager;
        $this->elementService = $elementService;
        $this->elementHistoryManager = $elementHistoryManager;
        $this->useElementLanguageAsFallback = $useElementLanguageAsFallback;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchingTreeIdsByLanguage($treeId, $maxDepth, $isPreview, $languages)
    {
        try {
            $tree = $this->treeManager->getByNodeId($treeId);
        } catch (Exception $e) {
            return null;
        }

        $iterator = new TreeIterator($tree->get($treeId));

        // create RecursiveIteratorIterator
        $rii = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
        if ($maxDepth !== null) {
            $rii->setMaxDepth($maxDepth);
        }

        $catched = array();
        foreach ($rii as $childNode) {
            /* @var $childNode TreeNodeInterface */

            if ($isPreview) {
                $availableLanguages = $childNode->getTree()->getSavedLanguages($childNode);
            } else {
                $availableLanguages = $tree->getPublishedLanguages($childNode);
            }

            foreach ($languages as $language) {
                if (in_array($language, $availableLanguages)) {
                    if (!isset($catched[$language])) {
                        $catched[$language] = array();
                    }

                    $catched[$language][] = $childNode->getId();
                    break;
                }
            }

            // if master language should be used as fallback
            // and child node was not found yet
            // -> use master language as fallback
            // TODO: problem - $language might be unset
            if ($this->useElementLanguageAsFallback
                    && (!isset($catched[$language]) || !in_array($childNode->getId(), $catched[$language]))) {
                $masterLanguage = $this->elementService
                    ->findElement($childNode->getTypeId())
                    ->getMasterLanguage();

                // master language is published
                // and master language was not processed yet
                if (in_array($masterLanguage, $availableLanguages) && !in_array($masterLanguage, $languages)) {
                    $catched[$masterLanguage][] = (int) $childNode->getId();
                }
            }
        }

        $matchedTreeIdsByLanguage = count($catched) ? $catched : null;

        return $matchedTreeIdsByLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function flatten(array $matchedTreeIdsByLanguage)
    {
        $matchedTreeIds = array();
        if (current($matchedTreeIdsByLanguage) !== '') {
            foreach ($matchedTreeIdsByLanguage as $treeIds) {
                $matchedTreeIds = array_merge($matchedTreeIds, $treeIds);
            }
        }

        return $matchedTreeIds;
    }
}
