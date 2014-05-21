<?php

namespace SatCMS\Modules\Core\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DoCommand extends Command {

    protected function configure() {
        $this
            ->setName('core:do')
            ->setDescription('Command proxy')
            ->addArgument(
                'action', InputArgument::REQUIRED, 'Command to run, e.g. sat.command'
            )
            ->addArgument(
                'params',
                InputArgument::IS_ARRAY,
                'command params'
            )


        ->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'be dry'
        );
        /*
            ->addOption(
                'quiet',
                null,
                InputOption::VALUE_NONE,
                'be quite'
            );
        */
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $action = $input->getArgument('action');

        $module = 'core';

        if (strpos($action, '.') !== false) {
            list($module, $action) = explode('.', $action);
        }

        $command = 'SatCMS\\Modules\\'
            . ucfirst($module)
            . '\\Commands\\'
            . ucfirst($action)
            . 'Command';

//        dd($input->getArguments());

        if (!class_exists($command)) {
            throw new \InvalidArgumentException('Bad command |' . $command);
        }

        with(new $command(uniqid('cmd_')))
            ->execute(
                $input, $output
            );

        /*
        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }
        */

        $output->writeln(print_r([$module, $action], 1));
    }
}