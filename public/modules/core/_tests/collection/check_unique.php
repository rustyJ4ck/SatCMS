<?php

// sqlite fails this tests

require('../loader.php');

$core = core::get_instance();

/** @var abs_collection $collection */
$collection = $core->model('test_images');

$collection->remove_all_fast();

test_assert($collection->check_unique_value('title', 'оДин'));

$collection->create(array('title' => 'Один'));
$collection->create(array('title' => 'Второй'));

test_assert(!$collection->check_unique_value('title', 'оДин'));

// is this necessary?
$db = core::lib('db');
$sql = "SELECT count(*) as count FROM sat_test_images WHERE LOWER(title) = 'один'";
$result = $db->fetch_row(
    $db->query($sql)
);

test_assert(1===$result['count']);
