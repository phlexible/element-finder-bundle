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
     * Filter result items
     *
     * @param ArrayCollection $items
     * @param ResultPool      $resultPool
     *
     * @return ArrayCollection
     */
    public function filterItems(ArrayCollection $items, ResultPool $resultPool);
}
