<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;

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
    public function getFacetNames();

    /**
     * Filter result items
     *
     * @param ArrayCollection $items
     * @param ResultPool      $resultPool
     *
     * @return ArrayCollection
     */
    public function reduceItems(ArrayCollection $items, ResultPool $resultPool);
}
