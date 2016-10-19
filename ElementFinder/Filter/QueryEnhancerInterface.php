<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;

/**
 * Query enhancer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface QueryEnhancerInterface extends FilterInterface
{
    /**
     * Enhance element finder query.
     *
     * @param ElementFinderConfig $config
     * @param QueryBuilder        $qb
     */
    public function enhanceQuery(ElementFinderConfig $config, QueryBuilder $qb);
}
