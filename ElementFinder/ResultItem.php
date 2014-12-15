<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

/**
 * Result item
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
     * @var int
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
     * @var \DateTime
     */
    private $publishedAt;

    /**
     * @var \DateTime
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
     * @param int       $treeId
     * @param int       $eid
     * @param int       $version
     * @param string    $language
     * @param int       $elementtypeId
     * @param bool      $isPreview
     * @param bool      $inNavigation
     * @param bool      $isRestricted
     * @param \DateTime $publishedAt
     * @param \DateTime $customDate
     * @param string    $sortField
     * @param array     $extras
     */
    public function __construct($treeId, $eid, $version, $language, $elementtypeId, $isPreview, $inNavigation, $isRestricted, $publishedAt, $customDate, $sortField, array $extras = array())
    {
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
     * @return \DateTime
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
     * @return int
     */
    public function getElementtypeId()
    {
        return $this->elementtypeId;
    }

    /**
     * @return boolean
     */
    public function isPreview()
    {
        return $this->isPreview;
    }

    /**
     * @return boolean
     */
    public function isInNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * @return boolean
     */
    public function isIsRestricted()
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
     * @return \DateTime
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