<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Tests\ElementFinder\Result;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor\ExecutionDescriptor;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor\ExecutionResult;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItem;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItemMapper;
use PHPUnit\Framework\TestCase;

/**
 * Result item mapper test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @covers \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItemMapper
 */
class ResultItemMapperTest extends TestCase
{
    public function testMapResult()
    {
        $descriptor = $this->prophesize(ExecutionDescriptor::class);

        $filters = array();
        $rows = array(
            array(
                'tree_id' => 123,
                'eid' => 234,
                'version' => 345,
                'language' => 'de',
                'elementtype_id' => 456,
                'is_preview' => true,
                'in_navigation' => true,
                'is_restricted' => true,
                'published_at' => '2001-02-03 04:05:06',
                'custom_date' => '2002-03-04 05:06:07',
                'sort_field' => 'testSortField',
                'extra1' => 999,
                'extra2' => 'extra',
                'extra3' => true,
                'extra4' => array(1, 2, 3),
            ),
        );

        $execResult = new ExecutionResult($descriptor->reveal(), $filters, $rows, 'testQuery');

        $mapper = new ResultItemMapper();
        $result = $mapper->mapResult($execResult);

        $expected = array(
            new ResultItem(
                123,
                234,
                345,
                'de',
                456,
                true,
                true,
                true,
                new \DateTime('2001-02-03 04:05:06'),
                new \DateTime('2002-03-04 05:06:07'),
                'testSortField',
                array(
                    'extra1' => 999,
                    'extra2' => 'extra',
                    'extra3' => true,
                    'extra4' => array(1, 2, 3),
                )
            ),
        );

        $this->assertEquals($expected, $result);
    }
}
