<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\RotationStrategy;

use Doctrine\Common\Cache\Cache;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;

/**
 * Cache rotations strategy.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheRotationStrategy implements RotationStrategyInterface
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastRotationPosition(ResultPool $pool)
    {
        $identifier = $pool->getHash();

        $position = $this->cache->fetch($identifier);

        if (!isset($position) || !$position) {
            $position = 0;
            $this->cache->save($identifier, $position);
        }

        return (int) $position;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastRotationPosition(ResultPool $pool, $position)
    {
        $identifier = $pool->getHash();

        $this->cache->save($identifier, $position);

        return $this;
    }
}
