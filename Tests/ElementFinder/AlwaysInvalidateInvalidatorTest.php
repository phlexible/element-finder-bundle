<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Tests\ElementFinder\Invalidator;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator\AlwaysInvalidateInvalidator;

/**
 * Always invalidate invalidator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AlwaysInvalidateInvalidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFresh()
    {
        $invalidator = new AlwaysInvalidateInvalidator();

        $this->assertFalse($invalidator->isFresh('test'));
    }
}
