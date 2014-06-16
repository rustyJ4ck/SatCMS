<?php

require "../loader.php";    

$core = core::get_instance(true);

test_assert($t=$core->T('_name'), $t);
test_assert($t=core::module('sat')->T('_name'), $t);
test_assert($t=core::module('users')->i18n->T('_name'), $t);
test_assert($t=core::module('users')->T('_name'), $t);

