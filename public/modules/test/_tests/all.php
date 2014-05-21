<?php

require __DIR__ . "/../../core/_tests/loader.php";

$module = core::module('test');

test_assert($module->get_name() == 'test');

test_assert($module->model('article')->get_ctype()->model == 'test.article');