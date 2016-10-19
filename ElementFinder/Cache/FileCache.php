<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Cache;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Dumper\XmlDumper;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\FilterManager;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Invalidator\InvalidatorInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Loader\XmlLoader;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool;
use Phlexible\Bundle\ElementFinderBundle\Exception\UnknownIdentifierException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * File based result pool cache.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileCache implements CacheInterface
{
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var InvalidatorInterface
     */
    private $invalidator;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param FilterManager        $filterManager
     * @param InvalidatorInterface $invalidator
     * @param string               $cacheDir
     */
    public function __construct(FilterManager $filterManager, InvalidatorInterface $invalidator, $cacheDir)
    {
        $this->filterManager = $filterManager;
        $this->invalidator = $invalidator;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($identifier)
    {
        $filename = $this->cacheDir."/$identifier.xml";

        if (!file_exists($filename)) {
            return false;
        }

        $time = filemtime($filename);

        return $this->invalidator->isFresh($time);
    }

    /**
     * {@inheritdoc}
     */
    public function put(ResultPool $resultPool)
    {
        $filesystem = new Filesystem();
        $dumper = new XmlDumper();

        $filename = "{$this->cacheDir}/{$resultPool->getIdentifier()}.xml";

        $filesystem->dumpFile($filename, $dumper->dump($resultPool));
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        $filename = $this->cacheDir."/$identifier.xml";

        if (!file_exists($filename)) {
            throw new UnknownIdentifierException("Result pool for identifier $identifier not found.");
        }

        $loader = new XmlLoader();
        $resultPool = $loader->load($this->filterManager, $filename);

        return $resultPool;
    }
}
