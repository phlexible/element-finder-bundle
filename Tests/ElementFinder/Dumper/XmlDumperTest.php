<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Tests\ElementFinder\Dumper;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Dumper\XmlDumper;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItem;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;
use PHPUnit\Framework\TestCase;

/**
 * Xml dumper test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @covers \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Dumper\XmlDumper
 */
class XmlDumperTest extends TestCase
{
    public function testDump()
    {
        $filter1 = $this->prophesize(FilterInterface::class);
        $filter2 = $this->prophesize(FilterInterface::class);

        $pool = new ResultPool(
            'foo',
            ElementFinderConfig::fromValues(array(
                'startTreeId' => 123,
                'elementtypeIds' => 'testElementtypeId1,testElementtypeId2',
                'maxDepth' => 234,
                'metaKey' => 'testMetaField',
                'metaKeywords' => 'testKeyword1,testKeyword2',
                'inNavigation' => true,
                'sortField' => 'testSortField',
                'sortDir' => 'DESC',
                'template' => 'testTemplate',
                'pageSize' => 345,
            )),
            array(
                'de',
            ),
            'testQuery',
            array(
                new ResultItem(1, 2, 3, 'de', 'foo', true, true, true, new \DateTime('2001-02-03 04:05:06'), new \DateTime('2002-03-04 05:06:07'), 'xx'),
                new ResultItem(2, 3, 4, 'en', 'bar', false, false, false, null, null, 'yy', array('extra1' => 'aa', 'extra2' => 11, 'extra3' => 22.5, 'extra4' => true, 'extra5' => array(1, 2, 3))),
            ),
            array(
                'testFilter1' => $filter1->reveal(),
                'testFilter2' => $filter2->reveal(),
            ),
            new \DateTime('2001-02-03 04:05:06')
        );

        $dumper = new XmlDumper();

        $result = $dumper->dump($pool);

        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<pool identifier="foo" createdAt="2001-02-03 04:05:06">
  <query>testQuery</query>
  <languages>
    <language>de</language>
  </languages>
  <config>
    <value key="treeId">123</value>
    <value key="elementtypeIds">testElementtypeId1,testElementtypeId2</value>
    <value key="maxDepth">234</value>
    <value key="metaField">testMetaField</value>
    <value key="metaKeywords">["testKeyword1","testKeyword2"]</value>
    <value key="navigation">1</value>
    <value key="sortField">testSortField</value>
    <value key="sortDir">DESC</value>
    <value key="template">testTemplate</value>
    <value key="pageSize">345</value>
  </config>
  <filters>
    <filter>testFilter1</filter>
    <filter>testFilter2</filter>
  </filters>
  <items>
    <item treeId="1" eid="2" version="3" language="de" elementtypeId="foo" isPreview="1" inNavigation="1" isRestricted="1" customDate="2002-03-04 05:06:07" publishedAt="2001-02-03 04:05:06" sortField="xx"/>
    <item treeId="2" eid="3" version="4" language="en" elementtypeId="bar" isPreview="0" inNavigation="0" isRestricted="0" customDate="" publishedAt="" sortField="yy">
      <extra key="extra1" type="string"><![CDATA[aa]]></extra>
      <extra key="extra2" type="integer"><![CDATA[11]]></extra>
      <extra key="extra3" type="double"><![CDATA[22.5]]></extra>
      <extra key="extra4" type="boolean"><![CDATA[1]]></extra>
      <extra key="extra5" type="array"><![CDATA[[1,2,3]]]></extra>
    </item>
  </items>
</pool>

EOF;

        $this->assertSame($expected, $result);
    }
}
