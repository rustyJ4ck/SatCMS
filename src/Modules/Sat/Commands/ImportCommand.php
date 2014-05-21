<?php

namespace SatCMS\Modules\Sat\Commands;

/*
use SatCMS\Modules\Core\Abstract\Runner as CommandRunner;
*/

use Symfony\Component\Console\Command\Command as CommandRunner;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends CommandRunner {

    protected function configure() {
        $this->setName('sat:import')
            ->setDescription('Import tool')

            ->addArgument('url', InputArgument::REQUIRED, 'Source url')
            ->addArgument('site_id', InputArgument::OPTIONAL, 'site ID', 1)
            ->addArgument('pid', InputArgument::OPTIONAL, 'parent node ID', 0)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface  $output) {

       $type = 'rss';
       with(new Import\Rss(
           $input->getArgument('site_id'),
           $input->getArgument('pid')
       ))
        ->import($input->getArgument('url'))
       ;

    }

}

