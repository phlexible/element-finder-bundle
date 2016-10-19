<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\Event\SaveNodeDataEvent;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\LookupBuilder;
use Phlexible\Bundle\GuiBundle\Properties\Properties;
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineEvent;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Node listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeListener implements EventSubscriberInterface
{
    /**
     * @var LookupBuilder
     */
    private $lookupBuilder;

    /**
     * @var Properties
     */
    private $properties;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TreeEvents::CREATE_NODE => 'onCreateNode',
            TreeEvents::CREATE_NODE_INSTANCE => 'onCreateNodeInstance',
            ElementEvents::SAVE_NODE_DATA => 'onSaveNodeData',
            TreeEvents::PUBLISH_NODE => 'onPublishNode',
            TreeEvents::SET_NODE_OFFLINE => 'onSetNodeOffline',
            TreeEvents::DELETE_NODE => 'onDeleteNode',
        );
    }

    /**
     * @param LookupBuilder $lookupBuilder
     * @param Properties    $properties
     */
    public function __construct(LookupBuilder $lookupBuilder, Properties $properties)
    {
        $this->lookupBuilder = $lookupBuilder;
        $this->properties = $properties;
    }

    /**
     * @param NodeEvent $event
     */
    public function onCreateNode(NodeEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->updatePreview($node);

        $this->storeTimestamp();
    }

    /**
     * @param NodeEvent $event
     */
    public function onCreateNodeInstance(NodeEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->updatePreview($node);

        $this->storeTimestamp();
    }

    /**
     * @param SaveNodeDataEvent $event
     */
    public function onSaveNodeData(SaveNodeDataEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->updatePreview($node);

        $this->storeTimestamp();
    }

    /**
     * @param NodeEvent $event
     */
    public function onPublishNode(NodeEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->updateOnline($node);

        $this->storeTimestamp();
    }

    /**
     * @param SetNodeOfflineEvent $event
     */
    public function onSetNodeOffline(SetNodeOfflineEvent $event)
    {
        $node = $event->getNode();
        $language = $event->getLanguage();

        $this->lookupBuilder->removeOnlineByTreeNodeAndLanguage($node, $language);

        $this->storeTimestamp();
    }

    /**
     * @param NodeEvent $event
     */
    public function onDeleteNode(NodeEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->remove($node);

        $this->storeTimestamp();
    }

    private function storeTimestamp()
    {
        $this->properties->set('element_finder', 'timestamp', time());
    }
}
