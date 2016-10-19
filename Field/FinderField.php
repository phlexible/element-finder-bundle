<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Field;

use Phlexible\Bundle\ElementtypeBundle\Field\AbstractField;

/**
 * FinderField
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FinderField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementfinder-finder-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'json';
    }
}
