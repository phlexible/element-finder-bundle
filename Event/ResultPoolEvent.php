<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Event;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;
use Symfony\Component\EventDispatcher\Event;

/**
 * Result pool event
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
