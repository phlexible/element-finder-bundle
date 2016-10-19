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

use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;

/**
 * Element finder that stores all assembled result pools for debug purposes.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DebugElementFinder extends ElementFinder
{
    /**
     * @var ResultPool[]
     */
    private $updatedResultPools = array();

    /**
     * @var ResultPool[]
     */
    private $cachedResultPools = array();

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
        $resultPool = parent::findByIdentifier($identifier);

        $this->cachedResultPools[] = $resultPool;

        return $resultPool;
    }

    /**
     * Find elements.
     *
     * @param ElementFinderConfig $config
     * @param array               $languages
     * @param bool                $isPreview
     *
     * @return ResultPool
     */
    public function find(ElementFinderConfig $config, array $languages, $isPreview)
    {
        $resultPool = parent::find($config, $languages, $isPreview);

        $this->updatedResultPools[] = $resultPool;

        return $resultPool;
    }
}
