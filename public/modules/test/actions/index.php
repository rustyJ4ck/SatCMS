<?php

class test_index_action extends controller_action {

    function run() {
        $this->renderer->return->content = __METHOD__;
    }

}