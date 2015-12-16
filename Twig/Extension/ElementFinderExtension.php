<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinder;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig element finder extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderExtension extends \Twig_Extension
{
    /**
     * @var ElementFinder
     */
    private $elementFinder;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param ElementFinder $elementFinder
     * @param RequestStack  $requestStack
     */
    public function __construct(ElementFinder $elementFinder, RequestStack $requestStack)
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
     * @return ResultPool
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
            throw new \Exception("No config values given to find().");
        }

        $config = ElementFinderConfig::fromValues($configValues);
        if ($pageSize) {
            $config->setPageSize($pageSize);
        }

        $resultPool = $this->elementFinder->find($config, $languages, $preview);

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
