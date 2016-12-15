<?php

/*
 * This file is part of the phlexible element finder package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementFinderBundle\Command;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinder;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that finds elements on given element finder configuration.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FindCommand extends Command
{
    /**
     * @var ElementFinder
     */
    private $finder;

    /**
     * @param ElementFinder $finder
     */
    public function __construct(ElementFinder $finder)
    {
        $this->finder = $finder;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element-finder:find')
            ->setDescription('Find elements.')
            ->addOption('tree-id', null, InputOption::VALUE_REQUIRED, 'Tree ID')
            ->addOption('preview', null, InputOption::VALUE_NONE, 'Preview')
            ->addOption('navigation', null, InputOption::VALUE_NONE, 'In navigation')
            ->addOption('language', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Language', array())
            ->addOption('elementtype-id', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Elementtype ID', array())
            ->addOption('filter', null, InputOption::VALUE_REQUIRED, 'Filter')
            ->addOption('max-depth', null, InputOption::VALUE_REQUIRED, 'Max. depth')
            ->addOption('parameter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Parameter', array())
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $treeId = $input->getOption('tree-id');
        $elementtypeIds = $input->getOption('elementtype-id');
        $filter = $input->getOption('filter');
        $maxDepth = $input->getOption('max-depth');
        $navigation = $input->getOption('navigation');
        $languages = $input->getOption('language');
        $preview = $input->getOption('preview');
        $parameters = $input->getOption('parameter');

        $config = new ElementFinderConfig();
        $config->setTreeId($treeId);
        $config->setElementtypeIds($elementtypeIds);
        $config->setFilter($filter);
        $config->setMaxDepth($maxDepth);
        $config->setNavigation($navigation);

        $resultPool = $this->finder->find($config, $languages, $preview);

        foreach ($parameters as $parameter) {
            $parts = explode('=', $parameter);
            if ($parts[0] && $parts[1]) {
                $resultPool->setParameter($parts[0], $parts[1]);
            }
        }

        $output->writeln('Result pool identifier: '.$resultPool->getIdentifier());
        $output->writeln('');

        $table = new Table($output);
        $table->setHeaders(array('Tree ID', 'Version', 'Language', 'Elementtype ID', 'Custom Date', 'Sort Field', 'Published At', 'Extras'));

        foreach ($resultPool->all() as $item) {
            $row = array(
                'treeId' => $item->getTreeId(),
                'version' => $item->getVersion(),
                'language' => $item->getLanguage(),
                'elementtypeId' => $item->getElementtypeId(),
                'customDate' => $item->getCustomDate() ? $item->getCustomDate()->format('Y-m-d H:i:s') : '-',
                'sortField' => $item->getSortField(),
                'publishedAt' => $item->getPublishedAt() ? $item->getPublishedAt()->format('Y-m-d H:i:s') : '-',
                'extra' => $item->getExtras() ? json_encode($item->getExtras()) : '-',
            );
            $table->addRow($row);
        }
        $table->render();

        $output->writeln('');
        $output->writeln('Facets:');
        foreach ($resultPool->getRawFacets() as $key => $facet) {
            $output->writeln("  $key:");
            foreach ($facet as $facetItem) {
                $value = $facetItem['value'];
                $count = $facetItem['count'];
                if ($value === null) {
                    $value = '[null]';
                }
                $output->writeln("    $value ($count)");
            }
        }

        if (count($parameters)) {
            $output->writeln('');
            $output->writeln('Filtered Facets:');
            foreach ($resultPool->getFacets() as $key => $facet) {
                $output->writeln("  $key:");
                foreach ($facet as $facetItem) {
                    $value = $facetItem['value'];
                    $count = $facetItem['count'];
                    if ($value === null) {
                        $value = '[null]';
                    }
                    $output->writeln("    $value ($count)");
                }
            }
        }
    }
}
