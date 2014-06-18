<?php

/**
* Test make_seo stuff
*/
                           
require('../loader.php');

$core = core::get_instance();

/** @var test_images_collection */
$ci = $core->model('test_images');

$ci->create(array(
    'title' => 'тест'
   , 'pid'   => 1
));

/*
$ci->get_last_item()

test_assert()
*/
