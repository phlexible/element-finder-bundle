<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Element finder message.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array(
            'channel' => 'element_finder',
        );
    }
}
