<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: patterns.php,v 1.3 2008/04/24 11:37:06 surg30n Exp $
 */


/**
 * helpers (laravel stuff)
 */

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  dynamic  mixed
     * @return void
     */
    function dd() {
        array_map(function ($x) {
            var_dump($x);
        }, func_get_args());
        die;
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities in a string.
     *
     * @param  string $value
     * @return string
     */
    function e($value) {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('with')) {
    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed $object
     * @return mixed
     */
    function with($object) {
        return $object;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value) {
        return $value instanceof Closure ? $value() : $value;
    }
}


if (!function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null) {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('array_set')) {
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    function array_set(&$array, $key, $value) {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (!function_exists('with')) {
    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed $object
     * @return mixed
     */
    function with($object) {
        return $object;
    }
}


/**
 * Singleton pattern
 * @package core
 */
abstract class singleton {
    
    static protected $_instance;

    static function get_instance($params = null) {
        return isset(static::$_instance)
            ? static::$_instance
            : (static::$_instance = new static ($params));
    }
    
}

/**
 * Class Reference
 * @package TF\Core\Base
 */
class reference implements \ArrayAccess{

    /** @var mixed reference &$data */
    private $_data;

    function __construct(&$data) {
        $this->_data = &$data;
    }

    static function make(&$data) {
        return new static($data);
    }

    function &resolve() {
        return $this->_data;
    }

    function set($v) {
        $this->_data = $v;
    }

    function get() {
        return $this->_data;
    }

    function __set($k , $v) {
        if (!$this->_data) {
            throw new tf_exception('Reference __set on empty ref');
        }
        if (is_object($this->_data)) {
            $this->_data->$k = $v;
        }
        if (is_array($this->_data)) {
            $this->_data[$k] = $v;
        }
    }

    function __get($k) {
        if (!$this->_data) {
            throw new tf_exception('Reference __get on empty ref');
        }
        if (is_object($this->_data)) {
            return $this->_data->$k;
        }
        if (is_array($this->_data)) {
            return $this->_data[$k];
        }
    }

    public function offsetSet($offset, $value) {

        $this->_check_array();
        $this->_data[$offset] = $value;
    }

    public function offsetExists($offset) {

        $this->_check_array();
        return array_key_exists($offset, $this->_data);
    }

    public function offsetUnset($offset) {

        $this->_check_array();
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset) {

        $this->_check_array();
        return array_key_exists($offset, $this->_data)
            ? $this->_data[$offset]
            : null;
    }

    private function _check_array() {
        if (!is_array($this->_data)) {
            throw new tf_exception(__METHOD__ . ' not array');
        }
    }

}

