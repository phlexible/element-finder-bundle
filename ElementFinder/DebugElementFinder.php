<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;

/**
 * Debug element finder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DebugElementFinder extends ElementFinder
{
    /**
     * @var ResultPool[]
     */
    private $updatedResultPools = array();

    /**
     * @var ResultPool[]
     */
    private $cachedResultPools = array();

    /**
     * @return int
     */
    public function countUpdatedResultPools()
    {
        return count($this->updatedResultPools);
    }

    /**
     * @return int
     */
    public function countCachedResultPools()
    {
        return count($this->cachedResultPools);
    }

    /**
     * @return ResultPool[]
     */
    public function getUpdatedResultPools()
    {
        return $this->updatedResultPools;
    }

    /**
     * @return ResultPool[]
     */
    public function getCachedResultPools()
    {
        return $this->cachedResultPools;
    }

    /**
     * @param string $identifier
     *
     * @return ResultPool
     */
    public function findByIdentifier($identifier)
    {
        $resultPool = parent::findByIdentifier($identifier);

        $this->cachedResultPools[] = $resultPool;

        return $resultPool;
    }

    /**
     * Find elements
     *
     * @param ElementFinderConfig $config
     * @param array               $languages
     * @param bool                $isPreview
     *
     * @return ResultPool
     */
    public function find(ElementFinderConfig $config, array $languages, $isPreview)
    {
        $resultPool = parent::find($config, $languages, $isPreview);

        $this->updatedResultPools[] = $resultPool;

        return $resultPool;
    }
}
