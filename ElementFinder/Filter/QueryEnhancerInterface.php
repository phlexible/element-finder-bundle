<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;

/**
 * Query enhancer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface QueryEnhancerInterface
{
    /**
     * Enhance element finder query.
     *
     * @param ElementFinderConfig $config
     * @param QueryBuilder        $qb
     */
    public function enhanceQuery(ElementFinderConfig $config, QueryBuilder $qb);
}
