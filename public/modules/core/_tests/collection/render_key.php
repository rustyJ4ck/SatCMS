<?php

require "../loader.php";


/** @var model_collection */
$collection = core::get_instance()->model('test_images');

$collection->load_from_array(
$array = array(
2 => array('id' => 3, 'title' => 'zz2'),
6 => array('id' => 4, 'title' => 'zz3'),
1 => array('id' => 1, 'title' => 'zz'),
3 => array('id' => 2, 'title' => 'zz1'),
4 => array('id' => 5, 'title' => 'zz4'),
5 => array('id' => 6, 'title' => 'zz5'),
));


$collection->is_render_by_key('title');

test_assert(!empty($collection->render()['zz2']));