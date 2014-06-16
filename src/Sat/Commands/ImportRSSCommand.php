<?php

/**
 * app sat:import-rss --database test --dry-run yes --clean yes "http://lenta.ru/rss" 1 1
 */

namespace SatCMS\Sat\Commands;

use SatCMS\Core\Commands\BaseCommand as CommandRunner;
use SatCMS\Sat\Import\RSSDriver;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportRSSCommand extends CommandRunner {

    protected function configure() {

        parent::configure();

        $this->setName('sat:import-rss')
            ->setDescription('Rss import tool')
            ->addArgument('url', InputArgument::REQUIRED, 'Source path')
            ->addArgument('site_id', InputArgument::OPTIONAL, 'site ID', 1)
            ->addArgument('pid', InputArgument::OPTIONAL, 'parent node ID', 0)
            ->addOption('clean', false, InputOption::VALUE_OPTIONAL, 'Cleanup', false)
            ->addOption('limit', false, InputOption::VALUE_OPTIONAL, 'Limit articles', 0)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface  $output) {

        $this->core();
        $this->prepare_db($input->getOption('database'));

        $importer = new RSSDriver(
            $input->getArgument('site_id'),
            $input->getArgument('pid')
        );

        $importer->dry_run($input->getOption('dry-run'));
        $importer->with_clean($input->getOption('clean'));

        $importer->import(
            $input->getArguments() + array()
        );

        $importer->done();

    }

}
