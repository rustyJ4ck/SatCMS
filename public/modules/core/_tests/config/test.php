<?php

require('../loader.php');

class test_config extends abs_config {};
$config = new test_config();

test_assert($config instanceof abs_config, 'abs_config');

test_assert(null === $config->cfg('test.data.id'), 'cache get 1');

$config->set_cfg_var('test.data.id', array('title' => 'hello'));

if (!test_assert(array('title' => 'hello') === ($test = $config->cfg('test.data.id')), 'cache get 2')) {
    test_print($test);
}

$config->set_cfg_var('simple', 657);

if (!test_assert(657 === ($test = $config->cfg('simple')), 'cache get 2.1')) {
    test_print($test);
}

$config->unset_cfg_var('test.data.id');

test_assert(null === $config->cfg('test.data.id'), 'cache get 3');
