<?php

require "../loader.php";

$i = core::get_instance()->class_register('test_images');

$i->create(array(
    'image' => array(
        'size' => 1
        , 'name' => '1.txt'
        , 'tmp_name' => 'E:/1.txt'
    )
));

$item = $i->get_last_item();

var_dump($item->image, $item->render());