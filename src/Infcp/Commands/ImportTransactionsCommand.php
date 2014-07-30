<?php

namespace SatCMS\Infcp\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use SatCMS\Core\Commands\BaseCommand;

class ImportTransactionsCommand extends BaseCommand {

    protected $name = 'infcp:importtransactions';

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln('Import cards');

        // init
        $this->core();

        $cards = \core::module('infcp')->model('transaction');

        $file = \loader::get_root() . '_docs/transactions.json';

        $data = json_decode(file_get_contents($file));

        foreach ($data->Rows as $row) {
            $row = (array)$row;
            unset($row['id']);
            $row['cardno'] = substr($row['cardno'], 0, -4) . rand(1000,9999);
            $cards->create($row);
        }
    }
}