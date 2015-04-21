<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Command;

use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderConfig;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tipfinder\AppBundle\ElementFinder\Filter\DateFilter;

/**
 * Find command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FindCommand extends ContainerAwareCommand
{
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
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = $this->getContainer()->get('phlexible_element_finder.finder');

        $treeId = $input->getOption('tree-id');
        $elementtypeIds = $input->getOption('elementtype-id');
        $filter = $input->getOption('filter');
        $maxDepth = $input->getOption('max-depth');
        $navigation = $input->getOption('navigation');
        $languages = $input->getOption('language');
        $preview = $input->getOption('preview');

        $config = new ElementFinderConfig();
        $config->setTreeId($treeId);
        $config->setElementtypeIds($elementtypeIds);
        $config->setFilter($filter);
        $config->setMaxDepth($maxDepth);
        $config->setNavigation($navigation);

        $resultPool = $finder->find($config, $languages, $preview);

        $output->writeln('Results:');

        $table = new Table($output);
        $table->setHeaders(array('Tree ID', 'Version', 'Language', 'Elementtype ID', 'Custom Date', 'Sort Field', 'Published At', 'Extras'));

        foreach ($resultPool->all() as $item) {
            $row = array(
                'treeId'        => $item->getTreeId(),
                'version'       => $item->getVersion(),
                'language'      => $item->getLanguage(),
                'elementtypeId' => $item->getElementtypeId(),
                'customDate'    => $item->getCustomDate() ? $item->getCustomDate()->format('Y-m-d H:i:s') : '-',
                'sortField'     => $item->getSortField(),
                'publishedAt'   => $item->getPublishedAt() ? $item->getPublishedAt()->format('Y-m-d H:i:s') : '-',
                'extra'         => $item->getExtras() ? json_encode($item->getExtras()) : '-',
            );
            $table->addRow($row);
        }
        $table->render();

        $output->writeln('');
        $output->writeln('Facets:');
        foreach ($resultPool->getFacets() as $key => $facet) {
            $output->writeln(" $key");
            foreach ($facet as $key => $value) {
                $output->writeln("  $key: $value");
            }
        }
    }
}
