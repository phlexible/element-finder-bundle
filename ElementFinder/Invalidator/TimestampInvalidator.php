<?php
/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator;

use Phlexible\Bundle\GuiBundle\Properties\Properties;

/**
 * Invalidator that invalidates based on timestamps.
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

    /**
     * @return int
     */
    private function getCheckTimestamp()
    {
        if (!$this->checkTimestamp) {
            $this->checkTimestamp = (int) $this->properties->get('element_finder', 'timestamp');

            if (!$this->checkTimestamp) {
                $this->checkTimestamp = time();
                $this->properties->set('element_finder', 'timestamp', $this->checkTimestamp);
            }
        }

        return $this->checkTimestamp;
    }
}
