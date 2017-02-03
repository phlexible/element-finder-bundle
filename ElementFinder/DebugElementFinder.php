<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor\ExecutionDescriptor;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;

/**
 * Element finder that stores all assembled result pools for debugging purposes.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DebugElementFinder implements ElementFinderInterface
{
    /**
     * @var ElementFinderInterface
     */
    private $finder;

    /**
     * @var ResultPool[]
     */
    private $updatedResultPools = array();

    /**
     * @var ResultPool[]
     */
    private $cachedResultPools = array();

    /**
     * @param ElementFinderInterface $finder
     */
    public function __construct(ElementFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return int
     */
    public function countUpdatedResultPools()
    {
        return count($this->updatedResultPools);
    }

    /**
     * @return int
     */
    public function countCachedResultPools()
    {
        return count($this->cachedResultPools);
    }

    /**
     * @return ResultPool[]
     */
    public function getUpdatedResultPools()
    {
        return $this->updatedResultPools;
    }

    /**
     * @return ResultPool[]
     */
    public function getCachedResultPools()
    {
        return $this->cachedResultPools;
    }

    /**
     * @param string $identifier
     *
     * @return ResultPool
     */
    public function findByIdentifier($identifier)
    {
        $resultPool = $this->finder->findByIdentifier($identifier);

        $this->cachedResultPools[] = $resultPool;

        return $resultPool;
    }

    /**
     * Find elements.
     *
     * @param ExecutionDescriptor $descriptor
     *
     * @return ResultPool
     */
    public function find(ExecutionDescriptor $descriptor)
    {
        $resultPool = $this->finder->find($descriptor);

        $this->updatedResultPools[] = $resultPool;

        return $resultPool;
    }
}
