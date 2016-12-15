<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinderInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor\ExecutionDescriptor;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;
use Phlexible\Bundle\ElementFinderBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig extension for finding elements.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderExtension extends \Twig_Extension
{
    /**
     * @var ElementFinderInterface
     */
    private $elementFinder;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param ElementFinderInterface $elementFinder
     * @param RequestStack           $requestStack
     */
    public function __construct(ElementFinderInterface $elementFinder, RequestStack $requestStack)
    {
        $this->elementFinder = $elementFinder;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('find', array($this, 'find')),
            new \Twig_SimpleFunction('find_again', array($this, 'findAgain')),
        );
    }

    /**
     * @param ElementStructureValue|array $configValues
     * @param int                         $pageSize
     *
     * @return \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool
     *
     * @throws \Exception
     */
    public function find($configValues, $pageSize = null)
    {
        $currentRequest = $masterRequest = $this->requestStack->getCurrentRequest();
        if ($this->requestStack->getParentRequest()) {
            $masterRequest = $this->requestStack->getMasterRequest();
        }

        $languages = array($currentRequest->getLocale());
        $preview = $currentRequest->attributes->get('preview', false);

        if ($configValues instanceof ElementStructureValue) {
            $configValues = $configValues->getValue();
        } elseif (!is_array($configValues)) {
            throw new InvalidArgumentException('No valid configuration values given to find().');
        }

        $config = ElementFinderConfig::fromValues($configValues);
        if ($pageSize) {
            $config->setPageSize($pageSize);
        }

        $descriptor = new ExecutionDescriptor($config, $languages, $preview);
        $resultPool = $this->elementFinder->find($descriptor);

        $parameters = array_merge(
            $masterRequest->request->all(),
            $masterRequest->query->all()
        );

        $resultPool->setParameters($parameters);

        return $resultPool;
    }

    /**
     * @param string $identifier
     *
     * @return ResultPool
     */
    public function findAgain($identifier)
    {
        $masterRequest = $this->requestStack->getMasterRequest();

        $resultPool = $this->elementFinder->findByIdentifier($identifier);

        $parameters = array_merge(
            $masterRequest->query->all(),
            $masterRequest->request->all()
        );

        $resultPool->setParameters($parameters);

        return $resultPool;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_element_finder';
    }
}
