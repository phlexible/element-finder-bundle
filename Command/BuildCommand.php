<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Build command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BuildCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element-finder:build')
            ->setDescription('Refresh element finder lookup tables.')
            ->addOption('empty', null, InputOption::VALUE_NONE, 'Remove all lookup items before building');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $treeManager = $this->getContainer()->get('phlexible_tree.tree_manager');
        $lookupBuilder = $this->getContainer()->get('phlexible_element_finder.lookup_builder');

        if ($input->getOption('empty')) {
            $lookupBuilder->removeAll();
        }

        foreach ($treeManager->getAll() as $tree) {
            $rii = new \RecursiveIteratorIterator($tree->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $treeNode) {
                $lookupBuilder->update($treeNode);
            }
        }

        $output->writeln("Build finished.");

        return $output;
    }
}
