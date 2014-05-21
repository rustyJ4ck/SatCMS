<?php

define('TF_SKIP_DATABASE', true);

require('../loader.php');

/** @var MultiCacheFile */
$cacher = core::lib('cache')->get_file_handle();

test_assert($cacher instanceof MultiCacheFile, 'instance');

// cache for 3 sec
$cacher->set('test', array('title' => 'hello'), 3);

if (!test_assert(array('title' => 'hello') === ($test = $cacher->get('test')), 'cache get')) {
   test_print($test);
}

// sleep not kill cache, clean on next request
sleep(5);

test_assert(null === $cacher->get('test'), 'cache get timeout');

