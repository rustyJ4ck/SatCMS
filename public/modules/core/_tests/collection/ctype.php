<?php
                            
require('../loader.php');

$core = core::get_instance();

test_assert(function () use ($core) {
    $ctypes = $core->get_ctypes();
    return ($ctypes && $ctypes instanceof ctype_collection);
}, 'node ctype');

$node_ctype =   $core->get_ctype('sat.node');

test_assert($node_ctype && $node_ctype->id === 200, 'node ctype');

$node_ctype = $core->get_ctype(200, false);

test_assert($node_ctype && $node_ctype->model === 'sat.node', 'node ctype by ID');

if (!test_assert(($test = core::module('sat')->get_node_handle()->get_ctype()->id) === 200, 'model ctypeID')) {
  test_print($test);
}

if (!test_assert(($test = core::module('sat')->get_node_handle()->_get_ctype()) === 'sat.node', 'model ctype')) {
    test_print($test);
}
