<?php

require('../loader.php');

$core = core::get_instance();

/** @var model_collection $collection */
$collection = $core->model('test_images');

test_assert($collection->get_formats()->get('editor.list.text.hidden'));

test_assert(!$collection->get_field('text', 'hidden'));

$collection->set_format('editor');

test_assert(!$collection->get_field('text', 'hidden'));

$collection->set_format('editor.list');

test_assert($collection->get_field('text', 'hidden'));

$vf = $collection->get_field('virtual_field');

test_assert($vf['type'] == 'virtual');

$item = $collection->alloc(['id' => 2]);

// closure-virtual
test_assert('view#2' === $item->render('virtual_field'));

$collection->set_format();

test_assert(!$collection->get_field('text', 'hidden'));


//test_assert(false === $collection->get_formats(), 'null test #1');
//test_assert(false === $collection->get_item_by_prop('name', 8888), 'null test #2');