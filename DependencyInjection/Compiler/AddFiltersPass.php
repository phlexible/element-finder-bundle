<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add filters pass
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
                throw new \InvalidArgumentException("Missing attribute alias on filter $id.");
            }
            $filters[$attributes[0]['alias']] = new Reference($id);
        }
        $container->findDefinition('phlexible_element_finder.filter_manager')->replaceArgument(0, $filters);
    }
}
