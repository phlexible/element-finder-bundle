<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Tests\ElementFinder\Invalidator;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator\TimestampInvalidator;
use Phlexible\Bundle\GuiBundle\Properties\Properties;
use Prophecy\Argument;

/**
 * Timestamp invalidator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TimestampInvalidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFresh()
    {
        $properties = $this->prophesize(Properties::class);
        $properties->get('element_finder', 'timestamp')->willReturn(100);

        $invalidator = new TimestampInvalidator($properties->reveal());

        $this->assertTrue($invalidator->isFresh(200));
    }

    public function testIsFreshStoresNewProperty()
    {
        $properties = $this->prophesize(Properties::class);
        $properties->get('element_finder', 'timestamp')->willReturn(null);
        $properties->set('element_finder', 'timestamp', Argument::type('integer'))->shouldBeCalled();

        $invalidator = new TimestampInvalidator($properties->reveal());

        $this->assertFalse($invalidator->isFresh(200));
    }
}
