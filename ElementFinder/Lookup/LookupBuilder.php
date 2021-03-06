<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Lookup;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Meta\ElementMetaDataManager;
use Phlexible\Bundle\ElementBundle\Meta\ElementMetaSetResolver;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinderEvents;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderLookupElement;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderLookupMeta;
use Phlexible\Bundle\ElementFinderBundle\Event\UpdateLookupElement;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Lookup builder.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LookupBuilder
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementMetaSetResolver
     */
    private $metasetResolver;

    /**
     * @var ElementMetaDataManager
     */
    private $metadataManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $elementHistoryManager;

    /**
     * @param EntityManager                  $entityManager
     * @param ElementService                 $elementService
     * @param ElementMetaSetResolver         $metasetResolver
     * @param ElementMetaDataManager         $metadataManager
     * @param EventDispatcherInterface       $dispatcher
     * @param ElementHistoryManagerInterface $elementHistoryManager
     */
    public function __construct(
        EntityManager $entityManager,
        ElementService $elementService,
        ElementMetaSetResolver $metasetResolver,
        ElementMetaDataManager $metadataManager,
        EventDispatcherInterface $dispatcher,
        ElementHistoryManagerInterface $elementHistoryManager)
    {
        $this->entityManager = $entityManager;
        $this->elementService = $elementService;
        $this->metasetResolver = $metasetResolver;
        $this->metadataManager = $metadataManager;
        $this->dispatcher = $dispatcher;
        $this->elementHistoryManager = $elementHistoryManager;
    }

    /**
     * @return EntityRepository
     */
    private function getLookupElementRepository()
    {
        return $this->entityManager->getRepository(ElementFinderLookupElement::class);
    }

    /**
     * @return EntityRepository
     */
    private function getLookupMetaRepository()
    {
        return $this->entityManager->getRepository(ElementFinderLookupMeta::class);
    }

    /**
     * Remove all lookup items.
     */
    public function removeAll()
    {
        foreach ($this->getLookupElementRepository()->findAll() as $lookupElement) {
            $this->entityManager->remove($lookupElement);
        }

        foreach ($this->getLookupMetaRepository()->findAll() as $lookupMeta) {
            $this->entityManager->remove($lookupMeta);
        }

        $this->entityManager->flush();
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param bool              $flush
     */
    public function remove(TreeNodeInterface $treeNode, $flush = true)
    {
        foreach ($this->getLookupElementRepository()->findBy(array('treeId' => $treeNode->getId())) as $lookupElement) {
            $this->entityManager->remove($lookupElement);
        }

        foreach ($this->getLookupMetaRepository()->findBy(array('treeId' => $treeNode->getId())) as $lookupMeta) {
            $this->entityManager->remove($lookupMeta);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param bool              $flush
     */
    public function removePreview(TreeNodeInterface $treeNode, $flush = true)
    {
        foreach ($this->getLookupElementRepository()->findBy(array('treeId' => $treeNode->getId(), 'isPreview' => true)) as $lookupElement) {
            $this->entityManager->remove($lookupElement);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param bool              $flush
     */
    public function removeOnline(TreeNodeInterface $treeNode, $flush = true)
    {
        foreach ($this->getLookupElementRepository()->findBy(array('treeId' => $treeNode->getId(), 'isPreview' => false)) as $lookupElement) {
            $this->entityManager->remove($lookupElement);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     * @param bool              $flush
     */
    public function removeOnlineByTreeNodeAndLanguage(TreeNodeInterface $treeNode, $language, $flush = true)
    {
        foreach ($this->getLookupElementRepository()->findBy(array('treeId' => $treeNode->getId(), 'language' => $language, 'isPreview' => false)) as $lookupElement) {
            $this->entityManager->remove($lookupElement);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param int               $version
     * @param string            $language
     * @param bool              $flush
     */
    public function removeMetaByTreeNodeAndVersionAndLanguage(TreeNodeInterface $treeNode, $version, $language, $flush = true)
    {
        foreach ($this->getLookupMetaRepository()->findBy(array('treeId' => $treeNode->getId(), 'version' => $version, 'language' => $language)) as $lookupMeta) {
            $this->entityManager->remove($lookupMeta);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     */
    public function update(TreeNodeInterface $treeNode)
    {
        $this->updateOnline($treeNode);
        $this->updatePreview($treeNode);
    }

    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return int|null
     */
    public function updatePreview(TreeNodeInterface $treeNode)
    {
        // TODO: repair
        $event = new UpdateLookupElement($treeNode, true);
        if ($this->dispatcher->dispatch(ElementFinderEvents::BEFORE_UPDATE_LOOKUP_ELEMENT, $event)->isPropagationStopped()) {
            return null;
        }

        $element = $this->elementService->findElement($treeNode->getTypeId());
        $elementVersion = $this->elementService->findLatestElementVersion($element);

        foreach ($treeNode->getTree()->getSavedLanguages($treeNode) as $language) {
            $this->updateVersion(
                $treeNode,
                $element,
                $elementVersion,
                true,
                $language,
                null
            );
        }

        $event = new UpdateLookupElement($treeNode, true);
        $this->dispatcher->dispatch(ElementFinderEvents::UPDATE_LOOKUP_ELEMENT, $event);
    }

    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return int|null
     */
    public function updateOnline(TreeNodeInterface $treeNode)
    {
        $event = new UpdateLookupElement($treeNode, false);
        if ($this->dispatcher->dispatch(ElementFinderEvents::BEFORE_UPDATE_LOOKUP_ELEMENT, $event)->isPropagationStopped()) {
            return null;
        }

        $element = $this->elementService->findElement($treeNode->getTypeId());
        foreach ($treeNode->getTree()->getPublishedVersions($treeNode) as $language => $onlineVersion) {
            $elementVersion = $this->elementService->findElementVersion($element, $onlineVersion);

            $this->updateVersion(
                $treeNode,
                $element,
                $elementVersion,
                false,
                $language,
                $onlineVersion
            );
        }

        $event = new UpdateLookupElement($treeNode, false);
        $this->dispatcher->dispatch(ElementFinderEvents::UPDATE_LOOKUP_ELEMENT, $event);
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param Element           $element
     * @param ElementVersion    $elementVersion
     * @param bool              $preview
     * @param string            $language
     * @param int               $onlineVersion
     */
    private function updateVersion(
        TreeNodeInterface $treeNode,
        Element $element,
        ElementVersion $elementVersion,
        $preview,
        $language,
        $onlineVersion
    ) {
        $this->updateMeta($treeNode, $element, $elementVersion, $language);

        $lookupElement = $this->getLookupElementRepository()
            ->findOneBy(
                array(
                    'treeId' => $treeNode->getId(),
                    'isPreview' => $preview,
                    'language' => $language,
                )
            );

        if (!$lookupElement) {
            $lookupElement = new ElementFinderLookupElement();
        }

        $lookupElement
            ->setEid($element->getEid())
            ->setTreeId($treeNode->getId())
            ->setPath(implode(',', $treeNode->getTree()->getIdPath($treeNode)))
            ->setPublishedAt($elementVersion->getCreatedAt())
            ->setCustomDate($elementVersion->getCustomDate($language))
            ->setIsPreview($preview)
            ->setElementVersionId($elementVersion->getId())
            ->setElementtypeId($element->getElementtypeId())
            ->setVersion($elementVersion->getVersion())
            ->setLanguage($language)
            ->setInNavigation($treeNode->getInNavigation())
            ->setIsRestricted($treeNode->getNeedAuthentication())
            ->setCachedAt(new \DateTime());

        $this->entityManager->persist($lookupElement);
        $this->entityManager->flush($lookupElement);
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param Element           $element
     * @param ElementVersion    $elementVersion
     * @param string            $language
     */
    private function updateMeta(TreeNodeInterface $treeNode, Element $element, ElementVersion $elementVersion, $language)
    {
        $this->removeMetaByTreeNodeAndVersionAndLanguage($treeNode, $elementVersion->getVersion(), $language, false);

        $metaset = $this->metasetResolver->resolve($elementVersion);

        if (!$metaset) {
            return;
        }

        $metadata = $this->metadataManager->findByMetaSetAndElementVersion($metaset, $elementVersion);

        if (!$metadata) {
            return;
        }

        foreach ($metaset->getFields() as $field) {
            //        foreach ($metadata->getValues()[$language] as $name => $value) {
            $fieldId = $field->getId();
            $value = $metadata->get($field->getName(), $language);
            if (!$value) {
                continue;
            }

            $cleanString = str_replace(
                array(',', ';'),
                array('===', '==='),
                html_entity_decode($value, ENT_COMPAT, 'UTF-8')
            );

            $splitValues = explode('===', $cleanString);

            foreach ($splitValues as $splitValue) {
                $lookupMeta = new ElementFinderLookupMeta();
                $lookupMeta
                    ->setTreeId($treeNode->getId())
                    ->setEid($element->getEid())
                    ->setVersion($elementVersion->getVersion())
                    ->setLanguage($language)
                    ->setSetId($metaset->getId())
                    ->setField($fieldId)
                    ->setValue($splitValue);

                $this->entityManager->persist($lookupMeta);
            }
        }
    }
}
