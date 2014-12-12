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
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;
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
            new \Twig_SimpleFunction('finder', array($this, 'finder')),
        );
    }

    /**
     * @param ElementStructureValue|array $field
     *
     * @return ResultPool
     */
    public function finder($field)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
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
        $resultPool = $this->elementFinder->find($config, $languages, $preview);

        $parameters = array_merge(
            $currentRequest->query->all(),
            $currentRequest->request->all()
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