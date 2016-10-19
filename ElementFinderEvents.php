<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle;

/**
 * Element finder events.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderEvents
{
    /**
     * Fired after a find.
     */
    const FIND = 'phlexible_element_finder.find';

    /**
     * Fired before a lookup elementis updated.
     */
    const BEFORE_UPDATE_LOOKUP_ELEMENT = 'phlexible_element_finder.before_update_lookup_element';

    /**
     * Fired after a lookup element is updated.
     */
    const UPDATE_LOOKUP_ELEMENT = 'phlexible_element_finder.update_lookup_element';
}
