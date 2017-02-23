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

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator\TtlInvalidator;
use PHPUnit\Framework\TestCase;

/**
 * TTL invalidator test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @covers \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator\TtlInvalidator
 */
class TtlInvalidatorTest extends TestCase
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
