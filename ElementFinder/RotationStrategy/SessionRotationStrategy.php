<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\RotationStrategy;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Session rotations strategy.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SessionRotationStrategy implements RotationStrategyInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastRotationPosition(ResultPool $pool)
    {
        $identifier = $pool->getHash();

        $position = $this->session->get($identifier);

        if (!isset($position) || !$position) {
            $this->session->$identifier = $position = 0;
        }

        return  (int) $position;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastRotationPosition(ResultPool $pool, $position)
    {
        $identifier = $pool->getHash();

        $this->session->set($identifier, $position);

        return $this;
    }
}
