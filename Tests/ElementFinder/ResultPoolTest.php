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

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultItem;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;
use Prophecy\Argument;

/**
 * Result pool test.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ResultPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $extra
     *
     * @return ResultItem
     */
    private function createResultItem(array $extra)
    {
        return new ResultItem(
            1,
            2,
            3,
            'de',
            'a-b-c',
            true,
            true,
            false,
            new \DateTime(),
            new \DateTime(),
            'title',
            $extra
        );
    }

    public function testFacets()
    {
        $config = new ElementFinderConfig();

        $pool = new ResultPool(
            'testIdentifier',
            $config,
            array('en'),
            'testQuery',
            array(
                $this->createResultItem(array('foo' => 'abc')),
                $this->createResultItem(array('foo' => 'abc', 'baz' => 'def')),
                $this->createResultItem(array('bar' => 'ghi', 'foo' => 'jkl')),
            ),
            array(),
            new \DateTime()
        );

        $this->assertSame(array('bar', 'baz', 'foo'), $pool->getFacetNames());
        $this->assertSame(
            array(
                'bar' => array(
                    array('value' => null, 'count' => 2),
                    array('value' => 'ghi', 'count' => 1),
                ),
                'baz' => array(
                    array('value' => null, 'count' => 2),
                    array('value' => 'def', 'count' => 1),
                ),
                'foo' => array(
                    array('value' => 'abc', 'count' => 2),
                    array('value' => 'jkl', 'count' => 1),
                ),
            ),
            $pool->getFacets()
        );
        $this->assertSame(
            array(
                array('value' => 'abc', 'count' => 2),
                array('value' => 'jkl', 'count' => 1),
            ),
            $pool->getFacet('foo')
        );
        $this->assertSame(
            array(
                array('value' => 'abc', 'count' => 2),
                array('value' => 'jkl', 'count' => 1),
            ),
            $pool->getRawFacet('foo')
        );
    }

    public function testFilter()
    {
        $config = new ElementFinderConfig();

        $filter = $this->prophesize('Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\ResultPoolFilterInterface');

        $pool = new ResultPool(
            'testIdentifier',
            $config,
            array('en'),
            'testQuery',
            array(
                $this->createResultItem(array('foo' => 'abc')),
                $this->createResultItem(array('foo' => 'abc', 'baz' => 'def')),
                $this->createResultItem(array('bar' => 'ghi', 'foo' => 'jkl')),
            ),
            array(
                $filter->reveal(),
            ),
            new \DateTime()
        );

        $filter->filterItems(Argument::cetera())->shouldBeCalled()->willReturn(new ArrayCollection());

        $pool->all();
    }
}
