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
 * Invalidator that invalidates based on a time to live value.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TtlInvalidator implements InvalidatorInterface
{
    /**
     * @var int
     */
    private $ttl;

    /**
     * @param int $ttl
     */
    public function __construct($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($timestamp)
    {
        return $timestamp > time() - $this->ttl;
    }
}
