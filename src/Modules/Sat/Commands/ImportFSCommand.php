<?php

/**
 * app sat:import-fs --dry-run yes --database test "Q:\!archive\_docs\php"
 */

namespace SatCMS\Modules\Sat\Commands;

use SatCMS\Modules\Core\Commands\BaseCommand as CommandRunner;
use SatCMS\Modules\Sat\Import\FSDriver;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;


class ImportFSCommand extends CommandRunner {

    protected function configure() {

        parent::configure();

        $this->setName('sat:import-fs')
            ->setDescription('Filesystem import tool')
            ->addArgument('path', InputArgument::REQUIRED, 'Source path')
            ->addArgument('site_id', InputArgument::OPTIONAL, 'site ID', 1)
            ->addArgument('pid', InputArgument::OPTIONAL, 'parent node ID', 0)
            ->addOption('clean', false, InputOption::VALUE_OPTIONAL, 'Cleanup', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->core();
        $this->prepare_db($input->getOption('database'));

        $importer = new FSDriver(
            $input->getArgument('site_id'),
            $input->getArgument('pid')
        );

        $importer->dry_run($input->getOption('dry-run'));
        $importer->with_clean($input->getOption('clean'));

        $importer->import($input->getArguments());

        $importer->done();

    }

}
