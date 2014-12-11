<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Element finder asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderAssetProvider implements AssetProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        return array(
            '@PhlexibleElementFinderBundle/Resources/scripts-ux/Ext.ux.form.FinderField.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        return array(
            '@PhlexibleElementFinderBundle/Resources/styles/finder.css',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        return array(
            '@PhlexibleElementFinderBundle/Resources/scripts/Definitions.js',

            '@PhlexibleElementFinderBundle/Resources/scripts/ElementFinderConfigWindow.js',
            '@PhlexibleElementFinderBundle/Resources/scripts/ElementFinderConfigPanel.js',
            '@PhlexibleElementFinderBundle/Resources/scripts/NewCatchWindow.js',

            '@PhlexibleElementFinderBundle/Resources/scripts/configuration/FieldConfigurationFinder.js',

            '@PhlexibleElementFinderBundle/Resources/scripts/field/Finder.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return null;
    }
}
