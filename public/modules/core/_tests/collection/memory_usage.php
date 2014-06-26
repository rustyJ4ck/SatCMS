<?php

// do not run in suite
if (!empty($argv[1])) return;

require('../loader.php');

$core = core::get_instance();

$m = memory_get_usage();

test_print("PHP " . PHP_VERSION . " MEM: " .  sprintf('%u', $m));

/** @var model_collection */
$cdata = $core->model('test_images');

test_print("CREATE COLLECTION MEM " .  sprintf('%u', memory_get_usage() - $m));
 
$m = memory_get_usage();
    $item =
    $cdata
    ->set_where("id = %d", 1)
    ->set_limit(1)
    ->load()
    ->get_item();
    
    $item->title = uniqid();


test_print("LOAD ONE MEM: " . sprintf('%u', memory_get_usage() - $m));

$m = memory_get_usage();

$cdata->_fake_items(1000);
foreach ($cdata as $item) {
    $item->title = uniqid();
}

test_assert($cdata->count() == 1001);

test_print("FAKE 1000 MEM " . sprintf('%u', memory_get_usage() - $m));

/*

BOOT 5.3.10: 7088240
CREATE COLLECTION: 11624
LOAD ONE: 6416
FAKE 1000: 2661000 

BOOT 5.4.3: 3572872
CREATE COLLECTION: 11496
LOAD ONE: 5512
FAKE 1000: 1717136 

*/