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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tipfinder\AppBundle\ElementFinder\Filter\DateFilter;

/**
 * Test command
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element-finder:test')
            ->setDescription('Test teasers.')
            ->addOption('fresh', 'f', InputOption::VALUE_NONE, 'Skip cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();

        ini_set('memory_limit', -1);

        $finder = $this->getContainer()->get('phlexible_element_finder.finder');

        $config = new ElementFinderConfig();
        $config->setTreeId(48);
        $languages = array('de');
        $preview = true;
        $identifier = $finder->createIdentifier($config, $languages, $preview);

        $resultPool = null;
        if (!$input->getOption('fresh')) {
            try {
                $resultPool = $finder->findByIdentifier($identifier);

                $output->writeln("<info>Loaded pool $identifier cached on {$resultPool->getCreatedAt()->format('Y-m-d H:i:s')}</info>");
            } catch (\Exception $e) {

            }
        }

        if (!$resultPool) {
            $resultPool = $finder->find($config, $languages, $preview, array(new DateFilter()));
            $output->writeln("<info>Created pool $identifier</info>");
        }

        $resultPool->setParameter('date_location', 'Dormagen');

        $output->writeln('Items:');
        foreach ($resultPool->all() as $item) {
            $output->writeln(" {$item->getTreeId()} " . json_encode($item->getExtras()));
        }

        $output->writeln('Facets:');
        foreach ($resultPool->getFacets() as $key => $facet) {
            $output->writeln(" $key");
            foreach ($facet as $key => $value) {
                $output->writeln("  $key: $value");
            }
        }
    }
}
