<?php

require('../loader.php');

$core = core::get_instance();

$items = $core->model('test_images');

$items->append($items->alloc(array('id' => 1)));

test_assert($items->count() === 1, 'count');

/** @var model_item $item */
$item = $items->get_item();

$item->invokeMethod = function($item) {
    test_print('invokeMethod#' . $item->id);
    $item->hello = '@hello';
    return 'closure-compiled';
};

test_assert(!$item->get_data('hello'));

/*
test_except('collection_exception'
        , function() use ($item) { $item->get_data('hello');}
        , 'get-null');
*/


$items->invoke('invokeMethod');

// test_assert('@hello' === $item->hello, 'get 2');

test_print($item->render());

// test_assert($item->is_new(), 'new');

// PHP 5.2
//$users->invoke('dump');

// PHP 5.3
//$users->invoke(function($item){$item->dump();});