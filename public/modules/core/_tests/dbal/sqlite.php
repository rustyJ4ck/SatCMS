<?php

require('../loader.php');

/** @var sqlite3_db $db */
$connection = core::lib('db');

/*
var_dump(
$id = $connection->query('PRAGMA table_info (sat_sat_file)'),
    $connection->sql_error(),
    $connection->fetch_all('PRAGMA table_info (sat_sat_file)')
);
*/

// dd($connection->fetch_all("PRAGMA table_info('sat_test_images')"));

//test_assert($connection->query("PRAGMA table_info('sat_test_images')")->fetchAll()[0]['name'] == 'id');

test_assert($connection->fetch_all("SELECT * FROM sqlite_master WHERE type='table' AND name LIKE 'sat_test_images'")[0]['name'] == 'sat_test_images');

test_assert($connection->fetch_all("PRAGMA table_info('sat_test_images')")[0]['name'] == 'id');

// SELECT * FROM sqlite_master WHERE type='table' AND name LIKE 'sat_sat_file'



