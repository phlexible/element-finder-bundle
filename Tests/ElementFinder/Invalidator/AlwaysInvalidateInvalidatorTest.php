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

/**
 * Always invalidate invalidator test.
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
