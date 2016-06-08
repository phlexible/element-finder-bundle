<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Model;

/**
 * Element finder config
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderConfig
{
    const SORT_TITLE_BACKEND = '__backend_title';
    const SORT_TITLE_PAGE = '__page_title';
    const SORT_TITLE_NAVIGATION = '__navigation_title';
    const SORT_PUBLISH_DATE = '__publish_date';
    const SORT_CUSTOM_DATE = '__custom_date';

    /**
     * @var int
     */
    private $treeId;

    /**
     * @var array
     */
    private $elementtypeIds = array();

    /**
     * @var string
     */
    private $sortField;

    /**
     * @var string
     */
    private $sortDir;

    /**
     * @var string
     */
    private $filter;

    /**
     * @var string
     */
    private $template;

    /**
     * @var int|null
     */
    private $maxDepth;

    /**
     * @var bool
     */
    private $inNavigation;

    /**
     * @var array
     */
    private $metaField;

    /**
     * @var array
     */
    private $metaKeywords;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @param array $values
     *
     * @return ElementFinderConfig
     */
    public static function fromValues(array $values)
    {
        $elementtypeIds = !empty($values['elementtypeIds']) ? explode(',', $values['elementtypeIds']) : array();
        $inNavigation = !empty($values['inNavigation']);
        $maxDepth = strlen($values['maxDepth']) ? (int) $values['maxDepth'] : null;
        $filter = !empty($values['filter']) ? $values['filter'] : null;
        $sortField = !empty($values['sortField']) ? $values['sortField'] : null;
        $sortDir = !empty($values['sortDir']) ? $values['sortDir'] : null;
        $startTreeId = !empty($values['startTreeId']) ? $values['startTreeId'] : null;
        $metaField = !empty($values['metaKey']) ? $values['metaKey'] : null;
        $metaKeywords = !empty($values['metaKeywords']) ? explode(',', $values['metaKeywords']) : null;
        $template = !empty($values['template']) ? $values['template'] : null;
        $pageSize = !empty($values['pageSize']) ? $values['pageSize'] : null;

        $config = new ElementFinderConfig();
        $config
            ->setTreeId($startTreeId)
            ->setElementtypeIds($elementtypeIds)
            ->setNavigation($inNavigation)
            ->setMaxDepth($maxDepth)
            ->setFilter($filter)
            ->setSortField($sortField)
            ->setSortDir($sortDir)
            ->setMetaField($metaField)
            ->setMetaKeywords($metaKeywords)
            ->setTemplate($template)
            ->setPageSize($pageSize);

        return $config;
    }

    /**
     * @param int $treeId
     *
     * @return $this
     */
    public function setTreeId($treeId)
    {
        $this->treeId = (int) $treeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getTreeId()
    {
        return $this->treeId;
    }

    /**
     * @param array $elementtypeIds
     *
     * @return $this
     */
    public function setElementtypeIds(array $elementtypeIds)
    {
        $this->elementtypeIds = $elementtypeIds;

        return $this;
    }

    /**
     * @return array
     */
    public function getElementtypeIds()
    {
        return $this->elementtypeIds;
    }

    /**
     * @param string $sortField
     *
     * @return $this
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField ?: null;

        return $this;
    }

    /**
     * Get field to sort by.
     *
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param string $sortOrder ASC/DESC
     *
     * @return $this
     */
    public function setSortDir($sortOrder)
    {
        $this->sortDir = $sortOrder ? (strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC') : null;

        return $this;
    }

    /**
     * @return string ASC/DESC
     */
    public function getSortDir()
    {
        return $this->sortDir;
    }

    /**
     * @param string $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param int $maxDepth
     *
     * @return $this
     */
    public function setMaxDepth($maxDepth)
    {
        if (strlen($maxDepth)) {
            $this->maxDepth = (int) $maxDepth;
        } else {
            $this->maxDepth = null;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @param bool $inNavigation
     *
     * @return $this
     */
    public function setNavigation($inNavigation = true)
    {
        $this->inNavigation = (bool) $inNavigation;

        return $this;
    }

    /**
     * @return bool
     */
    public function inNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template ?: null;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param array $metaField
     *
     * @return $this
     */
    public function setMetaField($metaField)
    {
        $this->metaField = $metaField ?: null;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaField()
    {
        return $this->metaField;
    }

    /**
     * @param array $metaKeywords
     *
     * @return $this
     */
    public function setMetaKeywords(array $metaKeywords = null)
    {
        $this->metaKeywords = $metaKeywords ?: null;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;

        return $this;
    }
}
