<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator;

/**
 * TTL invalidator
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
