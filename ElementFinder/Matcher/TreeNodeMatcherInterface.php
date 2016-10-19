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

/**
 * Tree node matcher.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeNodeMatcherInterface
{
    /**
     * Traverse tree and find matching nodes.
     * - check max depth.
     *
     * @param int   $treeId
     * @param int   $maxDepth
     * @param bool  $isPreview
     * @param array $languages
     *
     * @return array
     */
    public function getMatchingTreeIdsByLanguage($treeId, $maxDepth, $isPreview, $languages);

    /**
     * Flatten matched tree ids by language to simple tree id array.
     *
     * @param array $matchedTreeIdsByLanguage
     *
     * @return array
     */
    public function flatten(array $matchedTreeIdsByLanguage);
}
