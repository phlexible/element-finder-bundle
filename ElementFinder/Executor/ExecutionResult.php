<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterInterface;

/**
 * Execution descriptor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExecutionResult
{
    /**
     * @var ExecutionDescriptor
     */
    private $descriptor;

    /**
     * @var FilterInterface[]
     */
    private $filters;

    /**
     * @var array
     */
    private $rows;

    /**
     * @var string
     */
    private $query;

    /**
     * @param ExecutionDescriptor $descriptor
     * @param FilterInterface[]   $filters
     * @param array               $rows
     * @param string              $query
     */
    public function __construct(ExecutionDescriptor $descriptor, array $filters, array $rows, $query)
    {
        $this->descriptor = $descriptor;
        $this->filters = $filters;
        $this->rows = $rows;
        $this->query = $query;
    }

    /**
     * @return ExecutionDescriptor
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
}
