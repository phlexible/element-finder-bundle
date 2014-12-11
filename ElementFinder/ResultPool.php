<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

use Pagerfanta\Adapter\AdapterInterface;

/**
 * Result pool
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResultPool implements \Countable, AdapterInterface
{
    /**
     * @var ResultItem[]
     */
    private $items = array();

    /**
     * @var mixed
     */
    private $filter;

    /**
     * @var string
     */
    private $query;

    /**
     * @param string $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param ResultItem[] $items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = array();

        foreach ($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    /**
     * @param ResultItem $item
     *
     * @return $this
     */
    public function addItem(ResultItem $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param mixed $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param int $from
     * @param int $to
     *
     * @return array
     */
    public function range($from, $to)
    {
        return array_slice($this->items, $from, $to - $from);
    }

    /**
     * @param int $from
     * @param int $length
     *
     * @return array
     */
    public function slice($from, $length)
    {
        return array_slice($this->items, $from, $length);
    }

    /**
     * @param int $pageSize
     * @param int $page
     *
     * @return array
     */
    public function page($pageSize, $page)
    {
        return array_slice($this->items, $page * $pageSize, $pageSize);
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
     * @param array $values
     *
     * @return array
     */
    public function getFilteredItems(array $values = array())
    {
        if (!$this->filter || !count($values)) {
            return $this->getItems();
        }

        return $this->filter->filterItems($this->getItems(), $values);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        return $this->count();
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        return $this->slice($offset, $length);
    }
}