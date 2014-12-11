<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\RotationStrategy;

use Doctrine\Common\Cache\Cache;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;

/**
 * Cache rotations strategy
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