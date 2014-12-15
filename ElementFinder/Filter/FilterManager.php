<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

/**
 * Filter manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FilterManager
{
    /**
     * @var FilterInterface[]
     */
    private $filters = array();

    /**
     * @param FilterInterface[] $filters
     */
    public function __construct(array $filters = array())
    {
        foreach ($filters as $name => $filter) {
            $this->addFilter($name, $filter);
        }
    }

    /**
     * @param string          $name
     * @param FilterInterface $filter
     *
     * @return $this
     */
    public function addFilter($name, FilterInterface $filter)
    {
        $this->filters[$name] = $filter;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return FilterInterface
     */
    public function get($name)
    {
        return $this->filters[$name];
    }

    /**
     * @return FilterInterface[]
     */
    public function all()
    {
        return $this->filters;
    }
}
