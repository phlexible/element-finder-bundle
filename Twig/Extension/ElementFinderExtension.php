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
        );
    }

    /**
     * @param ElementStructureValue|array $field
     * @param int                         $pageSize
     *
     * @return ResultPool
     */
    public function find($field, $pageSize = null)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $masterRequest = $this->requestStack->getMasterRequest();
        $languages = array('de'); //array($currentRequest->attributes->get('_locale', false))
        $preview = true; //$currentRequest->attributes->get('preview', false)

        if (is_array($field)) {
            $values = $field;
        } elseif ($field instanceof ElementStructureValue) {
            $values = $field->getValue();
        } else {
            return '';
        }

        $config = ElementFinderConfig::fromValues($values);
        if ($pageSize) {
            $config->setPageSize($pageSize);
        }

        $resultPool = $this->elementFinder->find($config, $languages, $preview);

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