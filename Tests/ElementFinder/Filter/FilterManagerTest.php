<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Tests\ElementFinder\Filter;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterManager;

/**
 * Filter manager test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @covers \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterManager
 */
class FilterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $filter = $this->prophesize(FilterInterface::class);

        $filterManager = new FilterManager(array('foo' => $filter->reveal()));

        $this->assertSame($filter->reveal(), $filterManager->get('foo'));
    }

    /**
     * @expectedException \Phlexible\Bundle\ElementFinderBundle\Exception\InvalidArgumentException
     */
    public function testGetThrowsException()
    {
        $filter = $this->prophesize(FilterInterface::class);

        $filterManager = new FilterManager(array('foo' => $filter->reveal()));

        $filterManager->get('bar');
    }

    public function testHas()
    {
        $filter = $this->prophesize(FilterInterface::class);

        $filterManager = new FilterManager(array('foo' => $filter->reveal()));

        $this->assertTrue($filterManager->has('foo'));
        $this->assertFalse($filterManager->has('bar'));
    }

    public function testAll()
    {
        $filter1 = $this->prophesize(FilterInterface::class);
        $filter2 = $this->prophesize(FilterInterface::class);
        $filters = array($filter1->reveal(), $filter2->reveal());

        $filterManager = new FilterManager($filters);

        foreach ($filterManager->all() as $filter) {
            $this->assertSame(array_shift($filters), $filter);
        }
    }
}
