<?php
                                              
define('TF_SKIP_DATABASE', true);
  
require('../loader.php');

/** @var MultiCacheApc */
$apc = core::lib('cache')->get_apc_handle();
$apc->set('test', array('hello'), 5);
var_dump($apc->get('test'));
/*
// sleep not kill cache, clean on next request
sleep(10);
*/
var_dump($apc->get('test'));
echo "done";