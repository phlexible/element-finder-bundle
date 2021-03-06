<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Cache;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;

/**
 * Result pool cache interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CacheInterface
{
    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function isFresh($identifier);

    /**
     * @param \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool $pool
     */
    public function put(ResultPool $pool);

    /**
     * @param string $identifier
     *
     * @return \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool
     */
    public function get($identifier);
}
