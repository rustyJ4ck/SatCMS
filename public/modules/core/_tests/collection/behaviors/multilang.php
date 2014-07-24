<?php

require '../../loader.php';

$core = core::get_instance();

/** @var model_collection $collection */
$collection = $core->model('test_images');

$collection->remove_all_fast();

$collection->create([
    'text' => 'Русский'
]);

$core->i18n->lang = 'en';

$collection->modify([
    'text' => 'Английский'
]);




