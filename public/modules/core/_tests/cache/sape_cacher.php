<?php

define('TF_SKIP_DATABASE', true);

require('../loader.php');

/** @var sape_cacher */
$cacher = core::lib('sape_cacher');

test_assert($cacher instanceof sape_cacher, 'instance');

// cache for 3 sec
$cacher->set('test/data', 'id', array('title' => 'hello'), 3);

if (!test_assert(array('title' => 'hello') === ($test = $cacher->get('test/data', 'id')), 'cache get')) {
    test_print($test);
}

// sleep not kill cache, clean on next request
sleep(5);

test_assert(null === $cacher->get('test/data', 'id'), 'cache get timeout');
