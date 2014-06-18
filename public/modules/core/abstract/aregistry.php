<?php

/**
 * Class ARegistry
 * @package TF\Core\Base
 * Support array.nested.keys
 */
class aregistry extends registry {

    function is_set($key) {

        if (!isset($this->_registry[$key])) {

            if (strpos($key, '.') === false) {
                return false;
            }

            if (is_null(array_get($this->_registry, $key, null))) {
                return false;
            }
        }

        return true;
    }

    function set($key, $val = null) {
        if (strpos($key, '.') === false) {
            $this->_registry[$key] = $val;
        } else {
            array_set($this->_registry, $key, $val);
        }

        return $this;
    }

    function get($key, $default = null) {
        return array_key_exists($key, $this->_registry)
            ? $this->_registry[$key]
            : array_get($this->_registry, $key, $default);
    }

    /**
     * Merge
     * @param $data
     * @return $this
     */
    function merge($data) {
        $this->_registry = functions::array_merge_recursive_distinct($this->_registry, $data);

        return $this;
    }

}