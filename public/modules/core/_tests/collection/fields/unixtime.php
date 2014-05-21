<?php

require('../../loader.php');

$core = core::get_instance();

/** @var test_images_collection $collection */
$collection = $core->model('test_images');

//
// $collection->append()
//

$collection->set_working_fields('update_date');

$date = '20.01.1983 10:30';

$ID = $collection->create(['update_date' => $date]);

$newbie = $collection->get_last_item();

$item = $collection->load_only_id($ID);

test_assert($ID && $item && $newbie && $item->id === $newbie->id);

/// 'default' => 'now' ///

test_print(
    $item->render('update_date'), $item->update_date
);

test_assert($item->update_date === strtotime($date));

test_assert(
    $date === $item->render('update_date')
    && $date === $newbie->render('update_date')
);