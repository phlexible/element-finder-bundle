<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FacetSorterInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\ResultPoolFilterInterface;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;

/**
 * Result pool
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResultPool implements \Countable
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var ElementFinderConfig
     */
    private $config;

    /**
     * @var ResultItem[]
     */
    private $items = array();

    /**
     * @var array
     */
    private $facetNames = array();

    /**
     * @var array
     */
    private $filters;

    /**
     * @var string
     */
    private $query;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @param string              $identifier
     * @param ElementFinderConfig $config
     * @param string              $query
     * @param ResultItem[]        $items
     * @param array               $filters
     * @param \DateTime           $createdAt
     */
    public function __construct($identifier, ElementFinderConfig $config, $query, array $items, array $filters, \DateTime $createdAt = null)
    {
        if (null === $createdAt) {
            $createdAt = new \DateTime;
        }

        $this->facetNames = array();
        foreach ($items as $item) {
            if (!$item instanceof ResultItem) {
                throw new \InvalidArgumentException("Invalid result item.");
            }
            $this->facetNames = array_unique(array_merge($this->facetNames, array_keys($item->getExtras())));
        }
        sort($this->facetNames);

        $this->identifier = $identifier;
        $this->config = $config;
        $this->query = $query;
        $this->filters = $filters;
        $this->createdAt = $createdAt;

        $this->setParameter('identifier', $identifier);

        $this->items = new ArrayCollection($items);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters = array())
    {
        foreach ($parameters as $key => $value) {
            $this->setParameter($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getParameter($key, $defaultValue = null)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        return $defaultValue;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return ElementFinderConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param ResultItem $item
     *
     * @return $this
     */
    public function remove(ResultItem $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }

        return $this;
    }

    /**
     * @return ArrayCollection|ResultItem[]
     */
    private function getItems()
    {
        $items = $this->items;

        foreach ($this->filters as $filter) {
            if ($filter instanceof ResultPoolFilterInterface) {
                $items = $filter->filterItems($items, $this);
            }
        }

        return $items;
    }

    /**
     * @return ResultItem
     */
    public function first()
    {
        return $this->getItems()->first();
    }

    /**
     * @return ResultItem[]
     */
    public function all()
    {
        return $this->getItems()->getValues();
    }

    /**
     * @return ResultItem[]
     */
    public function rawAll()
    {
        return $this->items->getValues();
    }

    /**
     * @param int $from
     * @param int $to
     *
     * @return ResultItem[]
     */
    public function range($from, $to)
    {
        return $this->getItems()->slice($from, $to - $from);
    }

    /**
     * @param int $from
     * @param int $length
     *
     * @return ResultItem[]
     */
    public function slice($from, $length)
    {
        return $this->getItems()->slice($from, $length);
    }

    /**
     * @param int $index
     *
     * @return ResultItem[]
     */
    public function one($index)
    {
        return current($this->getItems()->slice($index, 1));
    }

    /**
     * @param int $index
     *
     * @return bool
     */
    public function has($index)
    {
        return $this->getItems()->get($index) !== null;
    }

    /**
     * @param int $pageSize
     * @param int $page
     *
     * @return ResultItem[]
     */
    public function page($pageSize, $page)
    {
        return $this->getItems()->slice($page * $pageSize, $pageSize);
    }

    /**
     * @param int $pageSize
     *
     * @return int
     */
    public function pageCount($pageSize)
    {
        return ceil(count($this->getItems()) / $pageSize);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->getItems()->count();
    }

    /**
     * @return int
     */
    public function nextStart()
    {
        return $this->getParameter('finder_start', 0) + $this->getConfig()->getPageSize();
    }

    /**
     * @return bool
     */
    public function hasMore()
    {
        return $this->count() > $this->getParameter('finder_start', 0) + $this->getConfig()->getPageSize();
    }

    /**
     * @return array
     */
    public function getFacetNames()
    {
        return $this->facetNames;
    }

    /**
     * @return array
     */
    public function getFacets()
    {
        $facets = array();
        foreach ($this->facetNames as $facetName) {
            $facets[$facetName] = $this->getFacet($facetName);
        }

        ksort($facets);

        return $facets;
    }

    /**
     * @param string $facetName
     *
     * @return array
     */
    public function getFacet($facetName)
    {
        return $this->extractValues($facetName, $this->getItems());
    }

    /**
     * @return array
     */
    public function getRawFacets()
    {
        $facets = array();
        foreach ($this->facetNames as $facetName) {
            $facets[$facetName] = $this->getRawFacet($facetName);
        }

        ksort($facets);

        return $facets;
    }

    /**
     * @param string $facetName
     *
     * @return array
     */
    public function getRawFacet($facetName)
    {
        return $this->extractValues($facetName, $this->items);
    }

    /**
     * @param string          $facetName
     * @param ArrayCollection $items
     *
     * @return array
     */
    private function extractValues($facetName, ArrayCollection $items)
    {
        $values = array();
        foreach ($items as $item) {
            if (!isset($values[$item->getExtra($facetName)])) {
                $values[$item->getExtra($facetName)] = array('value' => $item->getExtra($facetName), 'count' => 1);
            } else {
                $values[$item->getExtra($facetName)]['count']++;
            }
        }

        ksort($values);

        foreach ($this->filters as $filter) {
            if ($filter instanceof FacetSorterInterface) {
                $values = $filter->sortFacet($facetName, $values);
            }
        }

        $values = array_values($values);

        return $values;
    }
}
