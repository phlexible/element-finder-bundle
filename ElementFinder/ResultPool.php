<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

use Doctrine\Common\Collections\ArrayCollection;
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

        foreach ($items as $item) {
            if (!$item instanceof ResultItem) {
                throw new \InvalidArgumentException("Invalid result item.");
            }
        }

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
                $filter->reduceItems($items, $this->parameters);
            }
        }

        return $items;
    }

    /**
     * @return ResultItem[]
     */
    public function all()
    {
        return $this->getItems()->getValues();
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
        return ceil(count($this->items) / $pageSize);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->items->count();
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
    public function getFacets()
    {
        $parameters = array();
        foreach ($this->filters as $filter) {
            if ($filter instanceof ResultPoolFilterInterface) {
                $parameters = array_merge($parameters, $filter->getParameters());
            }
        }

        $facets = array();
        foreach ($parameters as $parameter) {
            $facets[$parameter] = $this->getFacet($parameter);
        }

        ksort($facets);

        return $facets;
    }

    /**
     * @param string $parameter
     *
     * @return array
     */
    public function getFacet($parameter)
    {
        $values = array();
        foreach ($this->getItems() as $item) {
            if (!isset($values[$item->getExtra($parameter)])) {
                $values[$item->getExtra($parameter)] = 1;
            } else {
                $values[$item->getExtra($parameter)]++;
            }
        }

        ksort($values);

        return $values;
    }

    /**
     * @return array
     */
    public function getRawFacets()
    {
        $parameters = array();
        foreach ($this->filters as $filter) {
            if ($filter instanceof ResultPoolFilterInterface) {
                $parameters = array_merge($parameters, $filter->getParameters());
            }
        }

        $facets = array();
        foreach ($parameters as $parameter) {
            $facets[$parameter] = $this->getRawFacet($parameter);
        }

        ksort($facets);

        return $facets;
    }

    /**
     * @param string $parameter
     *
     * @return array
     */
    public function getRawFacet($parameter)
    {
        $values = array();
        foreach ($this->items as $item) {
            if (!isset($values[$item->getExtra($parameter)])) {
                $values[$item->getExtra($parameter)] = 1;
            } else {
                $values[$item->getExtra($parameter)]++;
            }
        }

        ksort($values);

        return $values;
    }
}