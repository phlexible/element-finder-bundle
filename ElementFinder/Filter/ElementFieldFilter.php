<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;

/**
 * Element field filter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFieldFilter implements QueryEnhancerInterface, ResultPoolFilterInterface
{
    /**
     * @var
     */
    private $structureId;
    /**
     * @var
     */
    private $fieldName;

    /**
     * @param string $structureId
     * @param string $fieldName
     */
    public function __construct($structureId, $fieldName)
    {
        $this->structureId = $structureId;
        $this->fieldName = $fieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function enhanceQuery(ElementFinderConfig $config, QueryBuilder $qb)
    {
        $qb
            ->addSelect("d_sv1.content AS {$this->fieldName}")
            ->leftJoin('lookup', 'element_structure', 'd_s', 'd_s.element_version_id = lookup.element_version_id')
            ->andWhere($qb->expr()->eq('d_s.ds_id', $qb->expr()->literal($this->structureId)))
            ->leftJoin('lookup', 'element_structure_value', 'd_sv1', 'd_sv1.structure_id = d_s.id')
            ->andWhere($qb->expr()->eq('d_sv1.name', $qb->expr()->literal($this->fieldName)));
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return array($this->fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function reduceItems(ArrayCollection $items, $parameters)
    {
        foreach ($items as $item) {
            if (!empty($parameters[$this->fieldName]) && $item->getExtra($this->fieldName) !== $parameters[$this->fieldName]) {
                $items->removeElement($item);
            }
        }
    }
}
