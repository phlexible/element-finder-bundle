<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Controller;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
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
 * @Security("is_granted('ROLE_ELEMENT_FINDER')")
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
        $elementtypeIds = $request->get('query');

        $elementSourceManager = $this->get('phlexible_element.element_source_manager');
        $fieldRegistry = $this->get('phlexible_elementtype.field.registry');

        $fields = array();

        if ($elementtypeIds) {
            $elementtypeIds = explode(',', $elementtypeIds);

            $dsIds = null;
            foreach ($elementtypeIds as $elementtypeId) {
                $elementtype = $elementSourceManager->findElementtype($elementtypeId);
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
                if (!in_array($field->getDataType(), array('string', 'float', 'integer', 'number', 'boolean'))) {
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
        $elementSourceManager = $this->get('phlexible_element.element_source_manager');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $elementtypes = $elementSourceManager->findElementtypesByType('full');

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
     * @Route("/metafields", name="elementfinder_catch_metafields")
     */
    public function metaFieldsAction()
    {
        $metasetManager = $this->get('phlexible_meta_set.meta_set_manager');

        $metaFields = array();
        foreach ($metasetManager->findAll() as $metaset) {
            foreach ($metaset->getFields() as $field) {
                $metaFields[] = array(
                    'id'   => $field->getId(),
                    'name' => $metaset->getName() . '/' . $field->getName()
                );
            }
        }

        return new JsonResponse(array('metakeys' => $metaFields));
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
        $metaKeywords = array();
        foreach (array_column($conn->fetchAll($qb->getSQL()), 'value') as $value) {
            $metaKeywords[] = array('keyword' => $value);
        }

        return new JsonResponse(array('meta_keywords' => $metaKeywords));
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

        $filterManager = $this->get('phlexible_element_finder.filter_manager');

        foreach ($filterManager->all() as $name => $filter) {
            $data[] = array(
                'id'   => $name,
                'name' => ucfirst(strtolower($name)),
            );
        }

        return new JsonResponse(array('filters' => $data));
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
        $metaField = $request->get('metaKey', null);
        $metaKeywords = $request->get('metaKeywords', null);
        $template = $request->get('template', null);
        $sortField = $request->get('sortField', null);
        $sortDir = $request->get('sortDir', null);
        $filter = $request->get('filter', null);

        $config = new ElementFinderConfig();
        $config
            ->setTreeId($treeId)
            ->setMaxDepth($maxDepth)
            ->setNavigation($inNavigation)
            ->setElementtypeIds($elementtypeIds)
            ->setMetaField($metaField)
            ->setMetaKeywords($metaKeywords ? explode(',', $metaKeywords) : null)
            ->setTemplate($template)
            ->setSortField($sortField)
            ->setSortDir($sortDir)
            ->setFilter($filter);

        $elementFinder = $this->get('phlexible_element_finder.finder');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $result = $elementFinder->find($config, array('de'), true);

        $data = array();
        foreach ($result->range(0, 10) as $resultItem) {
            $tree = $treeManager->getByNodeId($resultItem->getTreeId());
            $treeNode = $tree->get($resultItem->getTreeId());
            $element = $elementService->findElement($treeNode->getTypeId());
            $elementVersion = $elementService->findElementVersion($element, $resultItem->getVersion());

            $data[] = array(
                'id'            => $resultItem->getTreeId(),
                'version'       => $resultItem->getVersion(),
                'language'      => $resultItem->getLanguage(),
                'elementtypeId' => $resultItem->getElementtypeId(),
                'customDate'    => $resultItem->getCustomDate() ? $resultItem->getCustomDate()->format('Y-m-d H:i:s') : null,
                'publishedAt'   => $resultItem->getPublishedAt() ? $resultItem->getPublishedAt()->format('Y-m-d H:i:s') : null,
                'sortField'     => $resultItem->getSortField(),
                'isRestricted'  => $resultItem->isRestricted(),
                'isPreview'     => $resultItem->isPreview(),
                'inNavigation'  => $resultItem->isInNavigation(),
                'extras'        => $resultItem->getExtras(),
                'title'         => $elementVersion->getBackendTitle($resultItem->getLanguage()),
                'icon'          => $iconResolver->resolveTreeNode($treeNode, $resultItem->getLanguage()),
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
