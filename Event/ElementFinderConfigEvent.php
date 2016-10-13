<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Event;

use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element finder config event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderConfigEvent extends Event
{
    /**
     * @var ElementFinderConfig
     */
    private $elementFinderConfig;

    /**
     * @param \Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig $elementFinderConfig
     */
    public function __construct(ElementFinderConfig $elementFinderConfig)
    {
        $this->elementFinderConfig = $elementFinderConfig;
    }

    /**
     * @return \Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig
     */
    public function getTreeId()
    {
        return $this->elementFinderConfig;
    }
}
