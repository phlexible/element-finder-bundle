<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle;

/**
 * Element finder events
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
