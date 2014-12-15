<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle;

use Phlexible\Bundle\ElementFinderBundle\DependencyInjection\Compiler\AddFiltersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Element finder bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleElementFinderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddFiltersPass());
    }
}
