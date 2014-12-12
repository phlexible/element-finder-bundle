<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Loader;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;

/**
 * Loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * @param string $filename
     *
     * @return ResultPool
     */
    public function load($filename);
}