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
     * @var int
     */
    private $findByIdentifierCount = 0;

    /**
     * @var int
     */
    private $findCount = 0;

    /**
     * @var ResultPool[]
     */
    private $resultPools = array();

    /**
     * @return int
     */
    public function getFindByIdentifierCount()
    {
        return $this->findByIdentifierCount;
    }

    /**
     * @return int
     */
    public function getFindCount()
    {
        return $this->findCount;
    }

    /**
     * @return ResultPool[]
     */
    public function getResultPools()
    {
        return $this->resultPools;
    }

    /**
     * @param string $identifier
     *
     * @return ResultPool
     */
    public function findByIdentifier($identifier)
    {
        $this->findByIdentifierCount++;

        $resultPool = parent::findByIdentifier($identifier);

        $this->resultPools[] = $resultPool;

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
        $this->findCount++;

        $resultPool = parent::find($config, $languages, $isPreview);

        $this->resultPools[] = $resultPool;

        return $resultPool;
    }
}
