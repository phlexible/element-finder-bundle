<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Event;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Update lookup element.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UpdateLookupElement extends Event
{
    /**
     * @var TreeNodeInterface
     */
    private $treeNode;

    /**
     * @var bool
     */
    private $preview;

    /**
     * @param TreeNodeInterface $treeNode
     * @param bool              $preview
     */
    public function __construct(TreeNodeInterface $treeNode, $preview = false)
    {
        $this->treeNode = $treeNode;
        $this->preview = $preview;
    }

    /**
     * @return TreeNodeInterface
     */
    public function getTreeNode()
    {
        return $this->treeNode;
    }

    /**
     * @return bool
     */
    public function isPreview()
    {
        return $this->preview;
    }
}
