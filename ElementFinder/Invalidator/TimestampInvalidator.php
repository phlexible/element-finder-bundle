<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator;

use Phlexible\Bundle\GuiBundle\Properties\Properties;

/**
 * Timestamp invalidator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TimestampInvalidator implements InvalidatorInterface
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @var
     */
    private $checkTimestamp;

    /**
     * @param Properties $properties
     */
    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($timestamp)
    {
        $checkTimestamp = $this->getCheckTimestamp();

        return $timestamp > $checkTimestamp;
    }

    private function getCheckTimestamp()
    {
        if (!$this->checkTimestamp) {
            $this->checkTimestamp = (int) $this->properties->get('element_finder', 'timestamp');

            if (!$this->checkTimestamp) {
                $this->checkTimestamp = time();
            }
        }

        return $this->checkTimestamp;
    }
}
