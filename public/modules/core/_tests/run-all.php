<?php

/**
 * Runs test in concurrent queue
 *
 * @param chroot directory core/_tests/config
 */

use Symfony\Component\Process\Process;

require "loader.php";

ob_implicit_flush(true);

$out = array();

$sourceDir = __DIR__;

if (!empty($argv[1])) {
   $sourceDir = loader::get_public('modules/' . $argv[1]);
}

fs::build_tree($sourceDir, $out, false /*[$sourceDir]*/, '\.php$');

// убираем loader и ru-all.php
foreach ($out['files'] as $k => $v) {
    if (preg_match('@(loader|run\-all)\.php$@', $v)) {
       unset($out['files'][$k]);
    }
}

// $files = array_splice($out['files'], 2);
$files = $out['files'];

test_assertions::$successed = 0;
test_assertions::$failed = 0;
test_assertions::$start_time = microtime(1);

$concurrent = 10;
$processes = [];

$file = null;
$kfile = 0;

$done_files = [];

/** @var $process Process */

test_head('TESTS: ' . count($files));

reset($files);

while (1) {

    while (count($processes) < $concurrent) {

        if ($file = next($files)) {

            $kfile++;

            // printf("* %4d (%d) %s \n", $kfile, count($processes), $file);

            // run test
            $process = new Process("php -f {$file} 2>&1", dirname($file));
            $processes []= [$process,  microtime(1), $kfile, $file];
            $process->start();

        }
        else {
            break;
        }
    }


    // main queue loop

    $loop = 1;
    while ($loop) {

        // query stat
        foreach ($processes as $pk => $_process) {
            list($process, $microtime, $key, $_file) = $_process;
            if (!$process->isRunning()) {
                $done_files []= $_file;
                $status = $process->isSuccessful();
                printf("%4s] %3d|%.4f %s \n"
                    , (!$status ? 'FAIL':'OKAY')
                    , $key
                    , microtime(1) - $microtime
                    , preg_replace('@^.*tests(.*)\s.*$@', '\\1', $process->getCommandLine())
                );

                test_assertions::$successed += ($status ? 1 : 0);
                test_assertions::$failed += ($status ? 0 : 1);

                unset($processes[$pk]);

                //done job, end loop (if queue empty, wait for last items)
                if (!$file) $loop = false;
            }
        }

        // last one
        if (empty($processes)) {
            $loop = false;
        }

        usleep(100);
    }

    // last one
    if (!$file && empty($processes)) {
        break;
    }
}

/*
var_dump($done_files == $files, $processes, count($done_files),count($files), test_assertions::$successed + test_assertions::$failed);
*/