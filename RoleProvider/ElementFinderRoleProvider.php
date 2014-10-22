<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\RoleProvider;

use Phlexible\Bundle\GuiBundle\Security\RoleProvider\RoleProvider;

/**
 * Element finder role provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderRoleProvider extends RoleProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        return [
            'ROLE_ELEMENT_FINDER',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function exposeRoles()
    {
        return $this->provideRoles();
    }
}