<?php

/*
 * This file is part of the phlexible sitemap package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Test\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Phlexible\Bundle\ElementFinderBundle\DependencyInjection\Configuration;

/**
 * Configuration test
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 *
 * @covers \Phlexible\Bundle\ElementFinderBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testDefaultValues()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array()
            ),
            array(
                'use_master_language_as_fallback' => false,
                'cache' => array(
                    'service' => 'phlexible_element_finder.file_cache',
                    'dir' => '%kernel.cache_dir%/elementfinder',
                ),
                'invalidator' => array(
                    'service' => 'phlexible_element_finder.timestamp_invalidator',
                    'ttl' => 300,
                ),
            )
        );
    }

    public function testConfiguredValues()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array(
                    'use_master_language_as_fallback' => true,
                    'cache' => array(
                        'service' => 'testCacheService',
                        'dir' => 'testDir1',
                    ),
                    'invalidator' => array(
                        'service' => 'testInvalidatorService',
                        'ttl' => 999
                    ),
                )
            ),
            array(
                'use_master_language_as_fallback' => true,
                'cache' => array(
                    'service' => 'testCacheService',
                    'dir' => 'testDir1',
                ),
                'invalidator' => array(
                    'service' => 'testInvalidatorService',
                    'ttl' => 999,
                ),
            )
        );
    }
}
