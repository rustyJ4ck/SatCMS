<?php

require('../../loader.php');

$config = array(
  'engine' => 'pdo_sqlite'
, 'prefix'   => 'sat_'
, 'path' => "config/localhost/database.db"
);

$connection = db_loader::get_doctrine('sqlite', $config);

//$connection->connect();

// Doctrine\DBAL\Driver\PDOStatement

// $row = ($connection->query("SELECT * from sat_test_images")->fetch());

/*
dd($connection->createQueryBuilder()
    ->select('*')
    ->from('sat_test_images')
    ->execute()
    ->fetch()
);
*/

// test_assert(var_dump(), 'connected');
test_assert(!$connection->isConnected(), 'connected');
test_assert($connection->query("SELECT * FROM sqlite_master WHERE type='table' AND name LIKE 'sat_test_images'")->fetchAll()[0]['name'] == 'sat_test_images');
test_assert($connection->isConnected(), 'connected');

test_assert($connection->query("PRAGMA table_info('sat_test_images')")->fetchAll()[0]['name'] == 'id');

// dd($connection->query("PRAGMA index_info('sat_test_images')")->fetchAll());
