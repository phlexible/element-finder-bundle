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

/**
 * Pool rotation.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Rotation
{
    public function rotationKram()
    {
        // if _poolSize is given (indicationg rotating teasers)
        // and _poolSize differs from _maxElements
        // and we are in frontend ($this->page != 0)
        if ($elementCatch->hasRotation() && ($elementCatch->getMaxResults() < count($result)) && $page) {
            $reducedResult = array();
            $resultKeys = array_keys($result);
            $resultSize = count($resultKeys);

            // get last remembered rotation position
            $pos = $this->getLastRotationPosition() % $resultSize;

            $size = min(
                $elementCatch->getPoolSize() ?: PHP_INT_MAX,
                $elementCatch->getMaxResults(),
                $resultSize
            );

            for ($i = 0; $i < $size; ++$i) {
                $key = $resultKeys[$pos];
                $reducedResult[$key] = $this->result[$key];
                $pos = ($pos + 1) % $resultSize;
            }

            // remember rotation position
            // TODO: store somewhere else
            $this->setLastRotationPosition($pos);

            $result = $reducedResult;
        }
    }
}
