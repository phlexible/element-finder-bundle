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

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;

/**
 * Cache rotations strategy.
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
     * @param \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool $pool
     *
     * @return int
     */
    public function getLastRotationPosition(ResultPool $pool);
}
