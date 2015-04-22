<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

/**
 * Facet sorter interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FacetSorterInterface extends FilterInterface
{
    /**
     * Sort a facet.
     *
     * @param string $parameter
     * @param array  $values
     *
     * @return array
     */
    public function sortFacet($parameter, array $values);
}
