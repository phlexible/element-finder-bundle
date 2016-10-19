<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

/**
 * Facet sorter interface.
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
