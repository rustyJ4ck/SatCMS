<?php

require('../loader.php');

$node = core::module('sat')->get_node_handle();

test_assert(($t = $node->get_class()) === 'sat.node', 'node ctype: ' . $t);

$users = core::module('users')->get_users_handle();

$node = $users;

test_assert(($t = $node->get_class()) === 'users', 'user ctype: ' . $t);

$node = core::module('sat')->model('ctype');

test_assert(($t = $node->get_class()) === 'sat.ctype', 'node ctype: ' . $t);
