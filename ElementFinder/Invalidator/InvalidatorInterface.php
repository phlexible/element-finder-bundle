<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator;

/**
 * Invalidator interface
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
