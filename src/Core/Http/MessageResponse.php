<?php

namespace SatCMS\Core\Http;

class MessageResponse extends JsonResponse {

    /** @return self */
    function message($msg) {
        $this->data['message'] = $msg;
        return $this;
    }

    /** @return self */
    function result($res) {
        $this->data['status'] = $res;

        return $this;
    }

    /** @return self */
    function redirect($res) {
        $this->data['redirect'] = $res;

        return $this;
    }

    /** @return self */
    function set_ajax_validator($v) {
        $this->data['validator'] = $v;

        return $this;
    }

    /** @return self */
    function data($v) {
        $this->data['data'] = $v;

        return $this;
    }

}