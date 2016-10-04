<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Cache;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;

/**
 * Cache interface
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
     * @param ResultPool $pool
     */
    public function put(ResultPool $pool);

    /**
     * @param string $identifier
     *
     * @return ResultPool
     */
    public function get($identifier);
}
