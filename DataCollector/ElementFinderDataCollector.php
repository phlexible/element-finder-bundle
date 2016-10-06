<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\DataCollector;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\DebugElementFinder;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

/**
 * Element finder data collector
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var DebugElementFinder
     */
    private $elementFinder;

    /**
     * @param null|ElementFinder $elementFinder
     */
    public function __construct($elementFinder = null)
    {
        if (null !== $elementFinder && $elementFinder instanceof ElementFinder) {
            $this->elementFinder = $elementFinder;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // everything is done as late as possible
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        if (null !== $this->elementFinder) {
            $this->data['find_count'] = $this->elementFinder->getFindCount();
            $this->data['find_by_identifier_count'] = $this->elementFinder->getFindByIdentifierCount();
            $this->data['total_count'] = $this->data['find_count'] + $this->data['find_by_identifier_count'];

            $pools = array();

            foreach ($this->elementFinder->getResultPools() as $resultPool) {
                $config = $resultPool->getConfig();
                $pools[] = array(
                    'config' => array(
                        'elementtype_ids' => $config->getElementtypeIds(),
                        'filter' => $config->getFilter(),
                        'max_depth' => $config->getMaxDepth(),
                        'meta_field' => $config->getMetaField(),
                        'meta_keywords' => $config->getMetaKeywords(),
                        'page_size' => $config->getPageSize(),
                        'sort_field' => $config->getSortField(),
                        'sort_dir' => $config->getSortDir(),
                        'template' => $config->getTemplate(),
                        'tree_id' => $config->getTreeId(),
                    ),
                    'parameters' => $resultPool->getParameters(),
                    'filters' => $resultPool->getFilters(),
                    #'count' => count($resultPool),
                    #'facets' => $resultPool->getFacets(),
                    'raw_facets' => $resultPool->getRawFacets(),
                    'facet_names' => $resultPool->getFacetNames(),
                    'created_at' => $resultPool->getCreatedAt(),
                    'identifier' => $resultPool->getIdentifier(),
                );
            }

            $this->data['result_pools'] = $pools;
        }
    }

    /**
     * Gets the called events.
     *
     * @return array An array of called events
     *
     * @see TraceableEventDispatcherInterface
     */
    public function countAll()
    {
        return isset($this->data['total_count']) ? $this->data['total_count'] : 0;
    }

    /**
     * Gets the called events.
     *
     * @return array An array of called events
     *
     * @see TraceableEventDispatcherInterface
     */
    public function countFind()
    {
        return isset($this->data['find_count']) ? $this->data['find_count'] : 0;
    }

    /**
     * Gets the called events.
     *
     * @return array An array of called events
     *
     * @see TraceableEventDispatcherInterface
     */
    public function countFindByIdentifier()
    {
        return isset($this->data['find_by_identifier_count']) ? $this->data['find_by_identifier_count'] : 0;
    }

    /**
     * Gets the result pools.
     *
     * @return array An array of result pools
     */
    public function getResultPools()
    {
        return isset($this->data['result_pools']) ? $this->data['result_pools'] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'element_finder';
    }
}
