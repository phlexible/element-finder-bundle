<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Tests\ElementFinder;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\ElementFieldFilter;
use PHPUnit\Framework\TestCase;

/**
 * Element field filter test.
 *
 * @author Tim Hoepfner <thoepfner@brainbits.net>
 *
 * @covers \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\ElementFieldFilter
 */
class ElementFieldFilterTest extends TestCase
{
    public function testEnsureElementFieldFilterIsInstantiable()
    {
        new ElementFieldFilter('', '');
    }
}
