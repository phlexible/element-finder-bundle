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

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Cache\CacheInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor\ExecutionDescriptor;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor\ExecutorInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItemMapper;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;
use Phlexible\Bundle\ElementFinderBundle\ElementFinderEvents;
use Phlexible\Bundle\ElementFinderBundle\Event\ResultPoolEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element finder.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CachingElementFinder implements ElementFinderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ExecutorInterface
     */
    private $findExecutor;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var ResultItemMapper
     */
    private $mapper;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ExecutorInterface        $findExecutor
     * @param CacheInterface           $cache
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ExecutorInterface $findExecutor,
        CacheInterface $cache
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->findExecutor = $findExecutor;
        $this->cache = $cache;

        $this->mapper = new ResultItemMapper();
    }

    /**
     * @param string $identifier
     *
     * @return ResultPool
     */
    public function findByIdentifier($identifier)
    {
        $pool = $this->cache->get($identifier);
        $pool->sort();

        return $pool;
    }

    /**
     * Find elements.
     *
     * @param ExecutionDescriptor $descriptor
     *
     * @return ResultPool
     */
    public function find(ExecutionDescriptor $descriptor)
    {
        $identifier = $descriptor->hash();

        if ($this->cache->isFresh($identifier)) {
            return $this->cache->get($identifier);
        }

        $result = $this->findExecutor->execute($descriptor);

        $items = $this->mapper->mapResult($result);

        $resultPool = new ResultPool(
            $result->getDescriptor()->hash(),
            $result->getDescriptor()->getConfig(),
            $result->getDescriptor()->getLanguages(),
            $result->getQuery(),
            $items,
            $result->getFilters()
        );

        $this->cache->put($resultPool);

        $event = new ResultPoolEvent($resultPool);
        $this->eventDispatcher->dispatch(ElementFinderEvents::FIND, $event);

        return $resultPool;
    }
}
