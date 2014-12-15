<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pool filter interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ResultPoolFilterInterface extends FilterInterface
{
    /**
     * @return array
     */
    public function getParameters();

    /**
     * Filter result items
     *
     * @param ArrayCollection $items
     * @param array           $parameters
     */
    public function reduceItems(ArrayCollection $items, $parameters);
}
