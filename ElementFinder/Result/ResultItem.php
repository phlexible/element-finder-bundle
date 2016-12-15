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

use DateTimeInterface;

/**
 * Result item.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResultItem
{
    /**
     * @var int
     */
    private $treeId;

    /**
     * @var int
     */
    private $eid;

    /**
     * @var int
     */
    private $version;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $elementtypeId;

    /**
     * @var bool
     */
    private $inNavigation;

    /**
     * @var bool
     */
    private $isPreview;

    /**
     * @var bool
     */
    private $isRestricted;

    /**
     * @var DateTimeInterface|null
     */
    private $publishedAt;

    /**
     * @var DateTimeInterface|null
     */
    private $customDate;

    /**
     * @var string
     */
    private $sortField;

    /**
     * @var array
     */
    private $extras;

    /**
     * @param int                    $treeId
     * @param int                    $eid
     * @param int                    $version
     * @param string                 $language
     * @param string                 $elementtypeId
     * @param bool                   $isPreview
     * @param bool                   $inNavigation
     * @param bool                   $isRestricted
     * @param DateTimeInterface|null $publishedAt
     * @param DateTimeInterface|null $customDate
     * @param string                 $sortField
     * @param array                  $extras
     */
    public function __construct(
        $treeId,
        $eid,
        $version,
        $language,
        $elementtypeId,
        $isPreview,
        $inNavigation,
        $isRestricted,
        DateTimeInterface $publishedAt = null,
        DateTimeInterface $customDate = null,
        $sortField,
        array $extras = array()
    ) {
        $this->treeId = $treeId;
        $this->eid = $eid;
        $this->version = $version;
        $this->language = $language;
        $this->elementtypeId = $elementtypeId;
        $this->isPreview = $isPreview;
        $this->inNavigation = $inNavigation;
        $this->isRestricted = $isRestricted;
        $this->publishedAt = $publishedAt;
        $this->customDate = $customDate;
        $this->sortField = $sortField;
        $this->extras = $extras;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCustomDate()
    {
        return $this->customDate;
    }

    /**
     * @return int
     */
    public function getEid()
    {
        return $this->eid;
    }

    /**
     * @return string
     */
    public function getElementtypeId()
    {
        return $this->elementtypeId;
    }

    /**
     * @return bool
     */
    public function isPreview()
    {
        return $this->isPreview;
    }

    /**
     * @return bool
     */
    public function isInNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * @return bool
     */
    public function isRestricted()
    {
        return $this->isRestricted;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @return int
     */
    public function getTreeId()
    {
        return $this->treeId;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return string
     */
    public function getExtra($key, $default = null)
    {
        if (isset($this->extras[$key])) {
            return $this->extras[$key];
        }

        return $default;
    }
}
