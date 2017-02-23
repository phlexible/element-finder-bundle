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

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator\AlwaysInvalidateInvalidator;
use PHPUnit\Framework\TestCase;

/**
 * Always invalidate invalidator test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @covers \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator\AlwaysInvalidateInvalidator
 */
class AlwaysInvalidateInvalidatorTest extends TestCase
{
    public function testIsFresh()
    {
        $invalidator = new AlwaysInvalidateInvalidator();

        $this->assertFalse($invalidator->isFresh('test'));
    }
}
