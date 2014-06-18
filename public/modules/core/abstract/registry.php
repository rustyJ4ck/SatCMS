<?php

/**
 * Registry
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: registry.php,v 1.2 2010/07/21 17:57:16 surg30n Exp $
 */
  
 /**
 * Register pattern
 * @package core
 */
class registry implements IteratorAggregate, ArrayAccess, Countable {
    
    protected $_registry;
    
    public function __construct($array = array()) {
        $this->_registry = $array ?: array();
    }
    
    /* Required definition of interface IteratorAggregate */
    public function getIterator() {
       return new collection_iterator($this->_registry);
    }

    /**
     * @param $array
     */
    function from_array($array) {
        $this->_registry = $array;
    }

    /**
    * getter method
    *
    * @param string $index - get the value associated with $index
    * @return mixed
    */
    public function get($index) {
        return (isset($this->_registry[$index])) ? $this->_registry[$index] : null;
    }

    function merge($data) {
        $this->_registry = array_merge($this->_registry, $data);
        return $this;
    }

    function count() {
        return empty($this->_registry) ? 0 : count($this->_registry);
    }
    
    /**
     * setter method
     *
     * @param string $index The location in the ArrayObject in which to store
     *   the value.
     * @param mixed $value The object to store in the ArrayObject.
     * @return void
     */
    public function set($index, $value = null) {
        $this->_registry[$index] = $value;
        return $this;
    }

    /**
    * Clears registry|item
    * @return self
    */
    public function clear($index = null) {
        if (!isset($index)) {
            $this->_registry = array();
        }
        else {
            if (isset($this->_registry[$index]))
            unset($this->_registry[$index]);
        }
        return $this;
    }    
    
    /**
     * Returns TRUE if the $index is a named value in the registry,
     * or FALSE if $index was not found in the registry.
     *
     * @param  string $index
     * @return boolean
     */
    public function is_registered($index) {
        return $this->is_set($index);        
    }
    
    function is_set($index) {
        return (isset($this->_registry[$index]));
    }
    
    
    function __set($index, $object) {
            $this->set($index, $object);
    }
 
    function __unset($index) {
        if (isset($this->_registry[$index])) {
            unset($this->_registry[$index]);
        }
    }
 
    function __get($index) {
        return $this->get($index);
    }
 
    function __isset($index) {
        return $this->is_registered($index);
    }

    /**
    * is empty 
    */
    public function is_empty() {
        return empty($this->_registry);
    }
    
    /**
    * !!!move to extend!!!
    */
    public function as_array() {
        return $this->_registry;
    }
        
    private $_loop_index = 0;
    
    public function rewind() {
        $this->_loop_index = 0;
    }
    
    
    public function next($force_rewind = false) {

        if ($force_rewind) $this->rewind();        
        if (empty($this->_registry) || $this->_loop_index >= count($this->_registry)) return false;
        
        $keys = array_keys($this->_registry);
        $key = $keys[$this->_loop_index];
        if (isset($this->_registry[$key])) {
            $this->_loop_index++;
            return $this->_registry[$key];    
        } 
        else {
            // auto rewind on exit
            $this->rewind();
        }
        
        return false;        
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            throw new Exception(__METHOD__ . ': empty offset');
            $this->_registry[] = $value;
        } else {
            $this->_registry[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->_registry);
    }

    public function offsetUnset($offset) {
        unset($this->_registry[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->_registry[$offset]) ? $this->_registry[$offset] : null;
    }

    protected $_invoke_exception = false;

    /**
     * Run method on all children
     *
     * $users->invoke('dump');
     * $users->invoke(function($item){$item->dump();});
     *
     * @param mixed $method
     * @param mixed $params
     * @return abs_collection self
     */
    function invoke($method, $params = null) {

        $_method = $method;

        if ($this->count()) {

            foreach ($this as $item) {

                if (is_string($_method)) {
                    $method = array($item, $_method);

                    if (functions::is_callable($method)) {
                        call_user_func_array($method, $params);
                    }
                    else {
                        if ($this->_invoke_exception)
                        throw new Collection_Exception(__METHOD__ . ' not callable: ' . get_class($method[0]) . ':' . $method[1]);
                    }
                }
                else // closure pass selfie first param
                    if ($_method instanceof Closure) {
                        $params = array($item, $params);
                        call_user_func_array ($method, $params); // $method(...$params)
                    }
                    else {
                        if ($this->_invoke_exception)
                        throw new Collection_Exception(__METHOD__ . ' closure not callable');
                    }
            }
        }

        return $this;
    }

    
    /**
    * dump
    */
    public function dump() {
        foreach ($this->_registry as $k => $v) {
            core::dprint('mod_dump) ' . $k);    
        }
    }
}



