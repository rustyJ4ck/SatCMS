<?php

require "../loader.php";

$i = core::module('sat')->get_node_image_handle();

$i->create(array(
    'file' => array(
        'size' => 1
        , 'name' => '518.jpg'
        , 'tmp_name' => 'E:/tmp/auto/logan/export/img/518.jpg'
    )
));

$item = $i->get_last_item();

var_dump($item->render());