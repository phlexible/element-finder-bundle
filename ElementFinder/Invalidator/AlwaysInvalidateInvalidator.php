<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator;

/**
 * Always invalidate invalidator
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
