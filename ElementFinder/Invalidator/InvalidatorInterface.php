<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator;

/**
 * Invalidator interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface InvalidatorInterface
{
    /**
     * @param int $timestamp
     *
     * @return bool
     */
    public function isFresh($timestamp);
}
