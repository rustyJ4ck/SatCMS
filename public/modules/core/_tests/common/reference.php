<?php

require('../loader.php');

$reftest = new referenceTest;

$dataA = ['param' => 1];
$dataO = new stdClass(['param' => 1]);

$reftest->testA(reference::make($dataA));

test_assert($dataA['param'] === 'helloA');

$reftest->testO(reference::make($dataO));

test_assert($dataO->param === 'helloO');

test_except('tf_exception', function() use ($reftest, $dataO) {
    $reftest->testA(reference::make($dataO));
});


class referenceRunner {

    function testA($data) {
        $data['param'] = 'helloA';
    }

    function testO($data) {
        $data->param = 'helloO';
    }

}

class referenceTest {

    protected $rr;

    function __construct() {
        $this->rr = new referenceRunner;
    }

    function __call($k, $v) {
        call_user_func_array([$this->rr, $k], $v);
    }

}