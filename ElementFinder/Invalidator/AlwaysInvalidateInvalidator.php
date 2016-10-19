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
 * Invalidator that always invalidates.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AlwaysInvalidateInvalidator implements InvalidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isFresh($timestamp)
    {
        return false;
    }
}
