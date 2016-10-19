<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Event;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;
use Symfony\Component\EventDispatcher\Event;

/**
 * Result pool event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResultPoolEvent extends Event
{
    /**
     * @var ResultPool
     */
    private $resultPool;

    /**
     * @param ResultPool $resultPool
     */
    public function __construct(ResultPool $resultPool)
    {
        $this->resultPool = $resultPool;
    }

    /**
     * @return ResultPool
     */
    public function getResultPool()
    {
        return $this->resultPool;
    }
}
