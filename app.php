#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

// use SatCMS\Modules\Sat\Commands\TestCommand;

require "public/modules/core/loader.php";

$BOOT_OPTIONS = array(
    loader::OPTION_TESTING => true,
    loader::OPTION_NO_INIT => true,
    loader::OPTION_CORE_PARAMS => array(
        //'debug' => 1 //quite
    )
);

loader::bootstrap($BOOT_OPTIONS);

$application = new Application();

$root = __DIR__ . '/src/Modules/*/Commands';

$finder = new Finder();
$finder->files()->in($root)->name('*.php')->depth('== 0');

$commands = [];

/** @var \SplFileInfo  $file */
foreach ($finder as $file) {
    // ... do something
    preg_match('@(?<mod>\w+)[\\\/]Commands[\\\/](?<cmd>\w+)\.php$@', $file->getPathname(), $matches);
    $commands []= 'SatCMS\\Modules\\' . ucfirst($matches['mod'])
        . '\\Commands\\' . ucfirst($matches['cmd']); // . 'Command';
}

foreach ($commands as $command) {
    $application->add(new $command);
}

$application->run();

