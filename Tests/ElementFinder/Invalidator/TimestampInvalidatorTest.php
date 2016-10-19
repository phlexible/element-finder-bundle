<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Tests\ElementFinder\Invalidator;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator\TimestampInvalidator;
use Phlexible\Bundle\GuiBundle\Properties\Properties;
use Prophecy\Argument;

/**
 * Timestamp invalidator test.
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
