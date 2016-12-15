<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor\ExecutionResult;

/**
 * Result item mapper.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResultItemMapper
{
    /**
     * @param ExecutionResult $result
     *
     * @return ResultItem[]
     */
    public function mapResult(ExecutionResult $result)
    {
        $resultItems = array();

        foreach ($result->getRows() as $row) {
            $resultItems[] = $this->mapRow($row);
        }

        return $resultItems;
    }

    /**
     * @param array $row
     *
     * @return ResultItem
     */
    private function mapRow(array $row)
    {
        $treeId = $row['tree_id'];
        $eid = $row['eid'];
        $version = $row['version'];
        $language = $row['language'];
        $elementtypeId = $row['elementtype_id'];
        $isPreview = $row['is_preview'];
        $inNavigation = $row['in_navigation'];
        $isRestricted = $row['is_restricted'];
        $publishedAt = $row['published_at'] ? new \DateTime($row['published_at']) : null;
        $customDate = $row['custom_date'] ? new \DateTime($row['custom_date']) : null;
        $sortField = $row['sort_field'] ?: null;

        unset(
            $row['tree_id'], $row['eid'], $row['version'], $row['language'], $row['elementtype_id'], $row['is_preview'],
            $row['in_navigation'], $row['is_restricted'], $row['published_at'], $row['custom_date'], $row['sort_field']
        );

        return new ResultItem(
            $treeId,
            $eid,
            $version,
            $language,
            $elementtypeId,
            $isPreview,
            $inNavigation,
            $isRestricted,
            $publishedAt,
            $customDate,
            $sortField,
            $row
        );
    }
}
