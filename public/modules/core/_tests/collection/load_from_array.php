<?php
                            
require('../loader.php');

$core = core::selfie();

$colors = $core->model('test_images');

$colors->remove_all_fast();

    /*->is_render_by_key(false)
    ->set_where("pid = %d AND value <> 0", 12)
    ->set_order('date DESC')
    ->set_limit(2)
    ->load()*/;
 
$colors->load_from_array(array(
      array('id' => 1, 'title' => '1', 'text' => '#А')
    , array('id' => 2, 'title' => '2', 'text' => '#Б')
    , array('id' => 3, 'title' => '3', 'text' => '#В')
)); 

test_assert($colors->count() === 3);
test_assert($colors->get_item()->is_new());
test_assert($colors->get_item()->is_allocated());
test_assert($colors->get_item()->text == '#А');

$item = $colors->get_item();

$item->save();

test_assert(!$item->is_new());
test_assert(!$item->is_allocated());
test_assert($item->text == '#А');

$colors->clear()->load();

test_assert($colors->count() === 1);
test_assert(!$colors->get_item()->is_new());
test_assert(!$colors->get_item()->is_allocated());
test_assert($colors->get_item()->text == '#А');

$item = $colors->get_item();

$item->text = '#UЮ';
$item->save();

$item = $colors->clear()->load()->get_item();

test_assert($item->text == '#UЮ');