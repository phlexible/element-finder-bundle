<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor\ExecutionDescriptor;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;

/**
 * Element finder interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementFinderInterface
{
    /**
     * @param string $identifier
     *
     * @return ResultPool
     */
    public function findByIdentifier($identifier);

    /**
     * Find elements.
     *
     * @param ExecutionDescriptor $descriptor
     *
     * @return ResultPool
     */
    public function find(ExecutionDescriptor $descriptor);
}
