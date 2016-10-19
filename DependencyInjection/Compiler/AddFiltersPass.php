<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register element finder filters.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddFiltersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $filters = [];
        foreach ($container->findTaggedServiceIds('phlexible_element_finder.filter') as $id => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new \RuntimeException("Missing attribute alias on filter $id.");
            }
            $filters[$attributes[0]['alias']] = new Reference($id);
        }
        $container->findDefinition('phlexible_element_finder.filter_manager')->replaceArgument(0, $filters);
    }
}
