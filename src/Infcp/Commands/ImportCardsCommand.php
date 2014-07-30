<?php

namespace SatCMS\Infcp\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use SatCMS\Core\Commands\BaseCommand;

class ImportCardsCommand extends BaseCommand {

    protected $name = 'infcp:importcards';

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln('Import cards');

        // init
        $this->core();

        $cards = \core::module('infcp')->model('card');

        $file = \loader::get_root() . '_docs/cards.json';

        $data = json_decode(file_get_contents($file));

        foreach ($data->rows as $row) {
            $row = (array)$row;
            unset($row['id']);
            $row['cardnumber'] = substr($row['cardnumber'], 0, -4) . rand(1000,9999);
            $cards->create($row);
        }
    }
}