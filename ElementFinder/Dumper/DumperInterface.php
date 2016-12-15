<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Dumper;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;

/**
 * Result pool dumper interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * @param \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool $pool
     *
     * @return string
     */
    public function dump(ResultPool $pool);
}
