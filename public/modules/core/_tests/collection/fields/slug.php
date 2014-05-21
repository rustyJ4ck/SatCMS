<?php

require('../../loader.php');

$core = core::get_instance();

/** @var test_images_collection $collection */
$collection = $core->model('test_images');

$collection->remove_all_fast();

$collection->create(array(
    'title' => 'Оно%^&толе*'
    //, 'pid'   => 1
));

test_assert('онотоле' == $collection->get_last_item()->slug, 'slug');

$collection->update_vfs(function(&$fields){
    $fields['slug']['make_seo']['translit'] = true;
});

$collection->create(array(
    'title' => 'Онотоле'
    //, 'pid'   => 1
));

test_assert('onotole' == $collection->get_last_item()->slug, 'translit');

$collection->create(array(
    'title' => '^^$#Оно%^&толе*'
    //, 'pid'   => 1
));

test_assert(strpos($collection->get_last_item()->slug, 'onotole-') === 0, 'same slug');
