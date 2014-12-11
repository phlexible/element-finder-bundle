<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\RotationStrategy;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Cache rotations strategy
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