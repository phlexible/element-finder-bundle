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
     * Render finder
     *
     * @param Request $request
     * @param string  $identifier
     *
     * @return Response
     * @Route("/render/{identifier}", name="elementfinder_render")
     */
    public function renderAction(Request $request, $identifier)
    {
        $finder = $this->get('phlexible_element_finder.finder');

        $pool = $finder->findByIdentifier($identifier);

        $data = array(
            'finder' => $pool,
        );

        return $this->render($pool->getConfig()->getTemplate(), $data);
    }
}
