<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Executor;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterInterface;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;

/**
 * Execution descriptor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExecutionDescriptor
{
    /**
     * @var ElementFinderConfig
     */
    private $config;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var bool
     */
    private $isPreview;

    /**
     * @param ElementFinderConfig $config
     * @param array               $languages
     * @param bool                $isPreview
     */
    public function __construct(ElementFinderConfig $config, array $languages, $isPreview)
    {
        $this->config = $config;
        $this->languages = $languages;
        $this->isPreview = $isPreview;
    }

    /**
     * @return ElementFinderConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @return bool
     */
    public function isPreview()
    {
        return $this->isPreview;
    }

    /**
     * @return string
     */
    public function hash()
    {
        return hash(
            'sha1',
            serialize(array($this->getConfig(), $this->getLanguages(), $this->isPreview()))
        );
    }
}
