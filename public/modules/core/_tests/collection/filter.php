<?php

require('../loader.php');

$core = core::get_instance();

/** @var model_collection $collection */
$collection = $core->model('test_images');

$condition = '22\';11"`<>,.!#$\\:';

test_assert(
    '"22\'\';11"`<>,.!#$\:"' ===
    $collection->connection()->escape($condition),
    'condition.1'
);

$filter = $collection->get_filter();

$filter->set_filter('title', $condition, 'BEGINS', 'AND');

dd(
    $filter->get_filters(),
    $filter->apply_count(),
    $filter->get_collection()->get_last_query(),
    $filter->apply_count(),
    $filter->get_collection()->get_last_query()
);


