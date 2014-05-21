<?php

/**
 * Code generator
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: iterator.php,v 1.2 2010/07/21 17:57:20 surg30n Exp $
 */

class collection_iterator implements Iterator {

    private $var = array();

    public function __construct($array) {
        if (is_array($array)) {
            $this->var = $array;
        }
    }

    public function rewind() {
        reset($this->var);
    }

    public function key() {
        $var = key($this->var);

        return $var;
    }

    public function next() {
        $var = next($this->var);

        return $var;
    }

    public function valid() {
        $var = $this->current() !== false;

        return $var;
    }

    public function current() {
        $var = current($this->var);

        return $var;
    }
}       
 
