<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Tests\ElementFinder\Invalidator;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator\TtlInvalidator;

/**
 * TTL invalidator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TtlInvalidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFresh()
    {
        $invalidator = new TtlInvalidator(300);

        $this->assertTrue($invalidator->isFresh(time()));
    }

    public function testIsNotFresh()
    {
        $invalidator = new TtlInvalidator(100);

        $this->assertFalse($invalidator->isFresh(time() - 400));
    }
}
