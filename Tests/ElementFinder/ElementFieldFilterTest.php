<?php

namespace Phlexible\Bundle\ElementFinderBundle\Tests\ElementFinder;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\ElementFieldFilter;

/**
 * Class ElementFieldFilterTest
 *
 * @author Tim Hoepfner <thoepfner@brainbits.net>
 */
class ElementFieldFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testEnsureElementFieldFilterIsInstantiable()
    {
        new ElementFieldFilter('','');
    }
}
