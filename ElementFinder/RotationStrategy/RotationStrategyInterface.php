<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\RotationStrategy;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;


/**
 * Cache rotations strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RotationStrategyInterface
{
    /**
     * Get last remembered position for teaser rotation.
     *
     * @param ResultPool $pool
     * @param int        $position
     *
     * @return $this
     */
    public function setLastRotationPosition(ResultPool $pool, $position);

    /**
     * Get last remembered position for teaser rotation.
     *
     * @param ResultPool $pool
     *
     * @return int
     */
    public function getLastRotationPosition(ResultPool $pool);
}