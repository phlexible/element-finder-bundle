<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Element finder extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleElementFinderExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter(
            'phlexible_element_finder.use_master_language_as_fallback',
            $config['use_master_language_as_fallback']
        );
        $container->setParameter('phlexible_element_finder.cache_dir', $config['cache']['dir']);
        $container->setAlias('phlexible_element_finder.cache', $config['cache']['service']);
        $container->setParameter('phlexible_element_finder.invalidator_ttl', $config['invalidator']['ttl']);
        $container->setAlias('phlexible_element_finder.invalidator', $config['invalidator']['service']);

        $loader->load('services.yml');
        $loader->load('twig_extensions.yml');

        if ($container->getParameter('kernel.debug')) {
            $container->findDefinition('phlexible_element_finder.finder')
                ->setClass('Phlexible\Bundle\ElementFinderBundle\ElementFinder\DebugElementFinder');
        }
    }
}
