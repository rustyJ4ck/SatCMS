<?php

namespace SatCMS\Core\Http;

class MessageResponse extends JsonResponse {

    private $_data;

    public function __construct($data = null, $status = 200, $headers = array())
    {
        $this->_data = new \aregistry($data?:array());
        return parent::__construct($data, $status, $headers);
    }

    function _update() {
        $this->setData(
            array_merge(
                array('message' => '', 'status' => false),
                $this->_data->as_array()
            )
        );
    }

    /** @return self */
    function message($msg) {
        $this->_data->message = $msg;
        $this->_update();
        return $this;
    }

    /** @return self */
    function status($res) {
        $this->_data->status = $res;
        $this->_update();
        return $this;
    }

    /** @return self */
    function redirect($res) {
        $this->_data->redirect = $res;
        $this->_update();
        return $this;
    }

    /** @return self */
    function set_ajax_validator($v) {
        $this->data['validator'] = $v;

        return $this;
    }

    /** @return self */
    function data($v) {
        $this->_data->data = $v;
        $this->_update();
        return $this;
    }

}