<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Render controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/_finder")
 */
class RenderController extends Controller
{
    /**
     * Render finder pool as html
     *
     * @param Request $request
     * @param string  $identifier
     *
     * @return Response
     * @Route("/render/html/{identifier}", name="elementfinder_render")
     */
    public function htmlAction(Request $request, $identifier)
    {
        $finder = $this->get('phlexible_element_finder.finder');

        $resultPool = $finder->findByIdentifier($identifier);

        $parameters = array_merge(
            $request->query->all(),
            $request->request->all()
        );

        $resultPool->setParameters($parameters);

        $data = array(
            'pool'     => $resultPool,
            'start'    => !empty($parameters['finder_start']) ? $parameters['finder_start'] : 0,
        );

        return $this->render($resultPool->getConfig()->getTemplate(), $data);
    }

    /**
     * Render finder pool as html
     *
     * @param Request $request
     * @param string  $identifier
     *
     * @return JsonResponse
     * @Route("/render/json/{identifier}", name="elementfinder_render_json")
     */
    public function jsonAction(Request $request, $identifier)
    {
        $finder = $this->get('phlexible_element_finder.finder');

        $resultPool = $finder->findByIdentifier($identifier);

        $parameters = array_merge(
            $request->query->all(),
            $request->request->all()
        );

        $resultPool->setParameters($parameters);

        $data = array(
            'pool'  => $resultPool,
            'start' => !empty($parameters['finder_start']) ? $parameters['finder_start'] : 0,
        );

        return new JsonResponse(array(
            'view'   => $this->renderView($resultPool->getConfig()->getTemplate(), $data),
            'start'  => $resultPool->getParameter('finder_start', 0),
            'limit'  => $resultPool->getConfig()->getPageSize(),
            'facets' => $resultPool->getFacets(),
            'hasMore' => $resultPool->hasMore(),
        ));
    }
}
