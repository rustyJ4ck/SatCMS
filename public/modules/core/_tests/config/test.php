<?php

require('../loader.php');

$core = core::selfie();

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


// core config vars

$dconfig = $core->get_dyn_config();

$item = $dconfig->get_item_by_name('test_007');

if ($item) {
    $dconfig->remove($item->id);
}

$dconfig->update_param('test_007', '2');

$dconfig->load();

$item = $dconfig->get_item_by_name('test_007');

test_assert($item, 'load-cfg');

test_assert($item->b_system, 'system');

$item->b_system = false;
$item->save();

$dconfig->update_param('test_007', '3');

$dconfig->load();
$item = $dconfig->get_item_by_name('test_007');

test_assert(!$item->b_system, 'system');
test_assert($item->value === '3' , 'value', function($v) use ($item) { test_print('value: ', $item->as_array()); });
