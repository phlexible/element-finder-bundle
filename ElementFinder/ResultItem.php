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

class_alias(\Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItem::class, 'Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultItem');

@trigger_error('Use of \Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultItem is deprecated, use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItem instead.', E_USER_DEPRECATED);
