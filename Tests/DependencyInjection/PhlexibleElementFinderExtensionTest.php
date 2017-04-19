<?php

/*
 * This file is part of the phlexible sitemap package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Phlexible\Bundle\ElementFinderBundle\DependencyInjection\PhlexibleElementFinderExtension;

/**
 * Phlexible element finder extension test.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 *
 * @covers \Phlexible\Bundle\ElementFinderBundle\DependencyInjection\PhlexibleElementFinderExtension
 */
class PhlexibleElementFinderExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return array(
            new PhlexibleElementFinderExtension(),
        );
    }

    public function testContainerWithDefaultConfiguration()
    {
        $this->setParameter('kernel.debug', true);

        $this->load();

        $this->assertContainerBuilderHasParameter('phlexible_element_finder.use_master_language_as_fallback', false);
        $this->assertContainerBuilderHasParameter('phlexible_element_finder.cache_dir', '%kernel.cache_dir%/elementfinder');
        $this->assertContainerBuilderHasParameter('phlexible_element_finder.invalidator_ttl', 300);
        $this->assertContainerBuilderHasAlias('phlexible_element_finder.cache', 'phlexible_element_finder.file_cache');
        $this->assertContainerBuilderHasAlias('phlexible_element_finder.invalidator', 'phlexible_element_finder.timestamp_invalidator');
    }

    public function testContainerWithCustomerConfiguration()
    {
        $this->setParameter('kernel.debug', true);

        $this->load(array(
            'use_master_language_as_fallback' => true,
            'cache' => array(
                'service' => 'testCacheService',
                'dir' => 'testDir1',
            ),
            'invalidator' => array(
                'service' => 'testInvalidatorService',
                'ttl' => 999,
            ),
        ));

        $this->assertContainerBuilderHasParameter('phlexible_element_finder.use_master_language_as_fallback', true);
        $this->assertContainerBuilderHasParameter('phlexible_element_finder.cache_dir', 'testDir1');
        $this->assertContainerBuilderHasParameter('phlexible_element_finder.invalidator_ttl', 999);
        $this->assertContainerBuilderHasAlias('phlexible_element_finder.cache', 'testCacheService');
        $this->assertContainerBuilderHasAlias('phlexible_element_finder.invalidator', 'testInvalidatorService');
    }
}
