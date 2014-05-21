<?php
                            
require('../loader.php');

$core = core::selfie();

$ci = $core->model('test_images');
test_assert('test_images_collection' === get_class($ci));

$ci = $core->model('sat.sat_node');
test_assert('sat_node_collection' === get_class($ci));

$ci = $core->model(array('sat', 'sat_node'));
test_assert('sat_node_collection' === get_class($ci));