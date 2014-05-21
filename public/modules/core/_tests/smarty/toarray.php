<?php

require('../loader.php');

/** @var tf_renderer $r */
$r = core::lib('renderer');

/** @var Smarty3 $smarty */
$smarty = $r->get_parser();

if (!
test_assert(
    '{"first":0,"second":1}' ===
    ($test = $smarty->fetch('string:{"first: no, second: \'yes\'"|to_array|json_encode}')),
    'test boolean yes-no'
)
) {
    test_print($test);
}

if (!
test_assert(
    '{"first":"firstValue","second":"secondValue"}' ===
    ($test = $smarty->fetch('string:{"first: \'firstValue\', second: \'secondValue\'"|to_array|json_encode}'))
)
) {
    test_print($test);
}

if (!
test_assert(
    '{"first":"firstValue","second":"secondValue With Space","third":"thirdValue","fourth":"Fourth Value 3"}' ===
    ($test = $smarty->fetch('string:{"first: firstValue, second: \'secondValue With Space\', third: thirdValue, fourth: \'Fourth Value 3\'"|to_array|json_encode}'))
)
) {
    test_print($test);
}


if (!
test_assert(
    '{"title":"Hello","dialogClass":"modal-xxl","int":"65"}' ===
    ($test = $smarty->fetch('string:{"title: \'Hello\', dialogClass: \'modal-xxl\' , int : 65 "|to_array|json_encode}'))
)
) {
    test_print($test);
}

