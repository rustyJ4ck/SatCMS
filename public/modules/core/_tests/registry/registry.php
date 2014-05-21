<?php

require('../loader.php');

class test_config extends registry {};
$config = new test_config();

test_assert($config instanceof registry, 'registry');

test_assert(null === $config->test, 'registry get 1');

$config->test = array('title' => 'hello');

if (!test_assert(array('title' => 'hello') === ($test = $config->test), 'registry get 2')) {
    test_print($test);
}

$config->set('simple', 657);

if (!test_assert(657 === ($test = $config->get('simple')), 'registry get 2.1')) {
    test_print($test);
}

$config->clear('test');

test_assert(null === $config->get('test'), 'registry get 3');
test_assert(null === $config->test, 'registry get 4');


// aregistry

$ar = new aregistry();

$ar->set('key', 123);

$ar['sub'] = array('a1' => '-a1-');

test_assert(isset($ar['key']), 'isset1');
test_assert(!empty($ar['key']), 'isset2');
test_assert(isset($ar['sub']['a1']), 'isset3');

unset($ar['key']);

test_assert(!isset($ar['key']), 'isset4');

$ar->set('sub.a2', '44');

test_assert($ar['sub']['a2'] === '44');

// Indirect modification of overloaded element of aregistry has no effect
@$ar['sub']['a2'] = 1;

test_assert($ar['sub']['a2'] === '44');
