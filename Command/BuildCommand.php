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

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Lookup\LookupBuilder;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command that build element finder lookup tables.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BuildCommand extends Command
{
    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var LookupBuilder
     */
    private $lookupBuilder;

    /**
     * @param TreeManager   $treeManager
     * @param LookupBuilder $lookupBuilder
     */
    public function __construct(TreeManager $treeManager, LookupBuilder $lookupBuilder)
    {
        $this->treeManager = $treeManager;
        $this->lookupBuilder = $lookupBuilder;

        parent::__construct();
    }

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
        $style = new SymfonyStyle($input, $output);

        if ($input->getOption('empty')) {
            $this->lookupBuilder->removeAll();
        }

        foreach ($this->treeManager->getAll() as $tree) {
            $rii = new \RecursiveIteratorIterator($tree->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $treeNode) {
                $this->lookupBuilder->update($treeNode);
            }
        }

        $style->success('Build finished.');

        return $output;
    }
}
