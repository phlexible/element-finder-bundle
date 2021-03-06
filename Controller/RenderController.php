<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Render controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/_finder/render")
 */
class RenderController extends Controller
{
    /**
     * Render finder pool as html.
     *
     * @param Request $request
     * @param string  $identifier
     *
     * @return Response
     * @Route("/html/{_locale}/{identifier}", name="elementfinder_render")
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
            'pool' => $resultPool,
            'start' => !empty($parameters['finder_start']) ? $parameters['finder_start'] : 0,
        );

        return $this->render($resultPool->getConfig()->getTemplate(), $data);
    }

    /**
     * Render finder pool as html.
     *
     * @param Request $request
     * @param string  $identifier
     *
     * @return JsonResponse
     * @Route("/json/{_locale}/{identifier}", name="elementfinder_render_json")
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
            'pool' => $resultPool,
            'start' => !empty($parameters['finder_start']) ? $parameters['finder_start'] : 0,
        );

        return new JsonResponse(array(
            'view' => $this->renderView($resultPool->getConfig()->getTemplate(), $data),
            'start' => (int) $resultPool->getParameter('finder_start', 0),
            'limit' => $resultPool->getConfig()->getPageSize(),
            'facets' => $resultPool->getFacets(),
            'rawFacets' => $resultPool->getRawFacets(),
            'parameters' => $parameters,
            'hasMore' => $resultPool->hasMore(),
        ));
    }
}
