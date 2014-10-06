<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Controller;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Catch controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementfinder")
 * @Security("is_granted('teasers')")
 */
class CatchController extends Controller
{
    /**
     * List all sortable fields.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/sortfields", name="elementfinder_catch_sortfields")
     */
    public function sortfieldsAction(Request $request)
    {
        $query = $request->get('query');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $fieldRegistry = $this->get('phlexible_elementtype.field.registry');

        $fields = array();

        if ($query) {
            $elementtypeIds = explode(',', $query);

            $dsIds = null;
            foreach ($elementtypeIds as $elementtypeId) {
                $elementtype = $elementtypeService->findElementtype($elementtypeId);
                $elementtypeStructure = $elementtype->getStructure();

                $dsIds = (null === $dsIds)
                    ? $elementtypeStructure->getAllDsIds()
                    : array_intersect($dsIds, $elementtypeStructure->getAllDsIds());
            }

            foreach ($dsIds as $dsId) {
                $node = $elementtypeStructure->getNode($dsId);

                // skip fields without working title
                if (!strlen($node->getName())) {
                    continue;
                }

                // skip fields of types that cannot be sorted by
                static $skipFieldTypes = array(
                    'accordion',
                    'businesslogic',
                    'file',
                    'form',
                    'group',
                    'reference',
                    'referenceroot',
                    'root',
                    'tab',
                    'table',
                );

                $fieldType = $node->getType();
                $field = $fieldRegistry->getField($fieldType);
                if ($field->isContainer()) {
                    continue;
                }
                if (!in_array($field->getDataType(), array('string', 'float', 'integer', 'number', 'checkbox'))) {
                     continue;
                 }

                if (in_array($fieldType, $skipFieldTypes)) {
                    continue;
                }

                $fields[] = array(
                    'ds_id' => $dsId,
                    'title' => $node->getName() . ' (' . $node->getLabel('fieldLabel', $this->getUser()->getInterfaceLanguage('en')) . ')',
                    'icon'  => $field->getIcon(),
                );
            }

            array_multisort(array_column($fields, 'title'), $fields);
        }

        $translator = $this->get('translator');
        array_unshift(
            $fields,
            array(
                'ds_id' => ElementFinderConfig::SORT_TITLE_BACKEND,
                'title' => $translator->trans('elements.backend_title', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementFinderConfig::SORT_TITLE_PAGE,
                'title' => $translator->trans('elements.page_title', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementFinderConfig::SORT_TITLE_NAVIGATION,
                'title' => $translator->trans('elements.navigation_title', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementFinderConfig::SORT_PUBLISH_DATE,
                'title' => $translator->trans('elements.publish_date', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementFinderConfig::SORT_CUSTOM_DATE,
                'title' => $translator->trans('elements.custom_date', array(), 'gui'),
                'icon'  => '',
            )
        );

        return new ResultResponse(true, 'Matching fields.', $fields);
    }

    /**
     * List all element types
     *
     * @return JsonResponse
     * @Route("/elementtypes", name="elementfinder_catch_elementtypes")
     */
    public function elementtypesAction()
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $elementtypes = $elementtypeService->findElementtypeByType('full');

        $data = array();
        foreach ($elementtypes as $elementtype) {
            $data[$elementtype->getTitle() . $elementtype->getId()] = array(
                'id'    => $elementtype->getId(),
                'title' => $elementtype->getTitle(),
                'icon'  => $iconResolver->resolveElementtype($elementtype),
            );
        }
        ksort($data);
        $data = array_values($data);

        return new JsonResponse(array('elementtypes' => $data));
    }

    /**
     * @return JsonResponse
     * @Route("/metakeys", name="elementfinder_catch_metakeys")
     */
    public function metaKeysAction()
    {
        $metasetManager = $this->get('phlexible_meta_set.meta_set_manager');

        $metakeys = array();
        foreach ($metasetManager->findAll() as $metaset) {
            foreach ($metaset->getFields() as $field) {
                $metakeys[] = array(
                    'id'   => $field->getId(),
                    'name' => $metaset->getName() . '/' . $field->getName()
                );
            }
        }

        return new JsonResponse(array('metakeys' => $metakeys));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/metakeywords", name="elementfinder_catch_metakeywords")
     */
    public function metaKeywordsAction(Request $request)
    {
        $id = $request->get('id');
        $language = $request->get('language');

        $conn = $this->get('doctrine.dbal.default_connection');
        /* @var $conn Connection */
        $qb = $conn->createQueryBuilder();
        $qb
            ->select('DISTINCT em.value')
            ->from('element_meta', 'em')
            ->where($qb->expr()->eq('em.field_id', $qb->expr()->literal($id)))
            ->andWhere($qb->expr()->eq('em.language', $qb->expr()->literal($language)));

        // TODO: repair
        $keywords = array();
        foreach (array_column($conn->fetchAll($qb->getSQL()), 'value') as $value) {
            $keywords[]['keyword'] = $value;
        }

        return new JsonResponse(array('meta_keywords' => $keywords));
    }

    /**
     * List all available filters
     *
     * @return JsonResponse
     * @Route("/filters", name="elementfinder_catch_filters")
     */
    public function filtersAction()
    {
        $data = array();

        // @TODO: repair
        $filters = array();//$this->componentCallback->getCatchFilter();

        foreach ($filters as $name => $class) {
            $data[] = array(
                'name'  => $name,
                'class' => $class,
            );
        }

        return new JsonResponse(array('filters' => $data));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="elementfinder_catch_create")
     */
    public function createAction(Request $request)
    {
        $teaserService = $this->get('phlexible_teaser.teaser_service');

        $teaserService->createCatch(
            $request->get('tree_id'),
            $request->get('eid'),
            $request->get('layoutarea_id')
        );

        return new ResultResponse(true, 'Catch created.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="elementfinder_catch_save")
     */
    public function saveAction(Request $request)
    {
        $catchManager = $this->get('phlexible_element_finder.finder_manager');

        $notEmpty = true;
        $i = 1;

        $catchMetaSearch = array();
        do {
            if ($request->get('catch_meta_key_' . $i) &&
                $request->get('catch_meta_keywords_' . $i)
            ) {
                $catchMetaSearchKey = $this->filter($request->get('catch_meta_key_' . $i));
                $catchMetaSearchKeyword = $this->filter($request->get('catch_meta_keywords_' . $i));
                $i++;
                if (strlen($catchMetaSearchKey) && strlen($catchMetaSearchKeyword)) {
                    $catchMetaSearch[$catchMetaSearchKey] = $catchMetaSearchKeyword;
                }
            } else {
                $notEmpty = false;
            }
        } while ($notEmpty);

        $catch = $catchManager->findCatch($request->get('id'));

        $catch
            ->setTitle($request->get('title'))
            ->setTreeId($request->get('for_tree_id_hidden'))
            ->setElementtypeIds($request->get('catch_element_type_id') ? explode(',', $request->get('catch_element_type_id')) : array())
            ->setNavigation($request->get('catch_in_navigation') === 'on')
            ->setMaxDepth($request->get('catch_max_depth'))
            ->setSortField($request->get('catch_sort_field'))
            ->setSortOrder($request->get('catch_sort_order'))
            ->setFilter($request->get('catch_filter'))
            ->setMaxResults($request->get('catch_max_elements'))
            ->setRotation($request->get('catch_rotation') === 'on')
            ->setPoolSize($request->get('catch_pool_size'))
            ->setResultsPerPage($request->get('catch_elements_per_page'))
            ->setMetaSearch($catchMetaSearch);

        $catchManager->updateCatch($catch);

        return new ResultResponse(true, 'Catch created.');
    }

    /**
     * @param string $s
     *
     * @return string
     */
    private function filter($s)
    {
        return strip_tags(trim($s));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/preview", name="elementfinder_catch_preview")
     */
    public function previewAction(Request $request)
    {
        $treeId = $request->get('startTreeId', null);
        $maxDepth = $request->get('maxDepth', null);
        $inNavigation = $request->get('inNavigation', false);
        $elementtypeIds = trim($request->get('elementtypeIds', ''));
        if ($elementtypeIds) {
            $elementtypeIds = explode(',', $elementtypeIds);
        } else {
            $elementtypeIds = array();
        }
        $metaKey = $request->get('metaKey', null);
        $metaKeywords = $request->get('metaKeywords', null);
        $template = $request->get('template', null);
        $sortField = $request->get('sortField', null);
        $sortDir = $request->get('sortDir', null);

        $elementFinderConfig = new ElementFinderConfig();
        $elementFinderConfig
            ->setTreeId($treeId)
            ->setMaxDepth($maxDepth)
            ->setNavigation($inNavigation)
            ->setElementtypeIds($elementtypeIds)
            #->setMetaSearch($metaKey)
            #->setMetaKeywords($metaKeywords)
            ->setTemplate($template)
            ->setSortField($sortField)
            ->setSortDir($sortDir)
        ;

        $elementFinder = $this->get('phlexible_element_finder.finder');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $result = $elementFinder->find($elementFinderConfig, array('de'), true);

        $data = array();
        foreach ($result->range(0,10) as $resultItem) {
            $tree = $treeManager->getByNodeId($resultItem->getTreeId());
            $treeNode = $tree->get($resultItem->getTreeId());
            $element = $elementService->findElement($treeNode->getTypeId());
            $elementVersion = $elementService->findElementVersion($element, $resultItem->getVersion());

            $data[] = array(
                'id'       => $resultItem->getTreeId(),
                'version'  => $resultItem->getVersion(),
                'language' => $resultItem->getLanguage(),
                'title'    => $elementVersion->getBackendTitle($resultItem->getLanguage()),
                'icon'     => $iconResolver->resolveTreeNode($treeNode, $resultItem->getLanguage()),
            );
        }

        return new JsonResponse(
            array(
                'items' => $data,
                'total' => count($result),
                'query' => $result->getQuery(),
            )
        );
    }
}
