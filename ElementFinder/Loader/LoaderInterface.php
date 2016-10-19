<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Loader;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterManager;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;

/**
 * Loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * @param FilterManager $filterManager
     * @param string        $filename
     *
     * @return ResultPool
     */
    public function load(FilterManager $filterManager, $filename);
}
