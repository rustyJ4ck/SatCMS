<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: data.php,v 1.3.2.1.2.1 2012/04/09 07:44:40 Vova Exp $
 */  

/**
* Data registry wrapper
* @package core
*/  
abstract class abs_data {

      const DATA_NOT_FOUND = false;
    
      /**
      * Данные,
      * @access public for backward compatibility
      */         
      protected $data;
    
      /**
      * set data item by id
      */         
      function set_data($key, $val = null) {
          if (!isset($val) && is_array($key))
            $this->data = $key;
          else
            $this->data[$key] = $val;

          return $this;
      }      
      
      /**
      * get data item by key
      * @param mixed $key filter
      *  string = return value
      *  array  = return [[key => value], [key => value], ...]
      *  false  = return all data array
      * @return false|object false if no matched key @see isset_data
      */         
      function get_data($key = false) {

          if (is_null($key)) return self::DATA_NOT_FOUND;

          if ($key === false) return $this->data;

          if (is_string($key)) {
              return isset($this->data[$key]) ? $this->data[$key] : self::DATA_NOT_FOUND;
          }

          // filter

          if (is_array($key)) {
                $data = array();
                foreach ($key as $k) {
                   $data [$k]= @$this->data[$k];
                }
                return $data;
          }

          return self::DATA_NOT_FOUND;
      }
      
      /**
      * check is data set
      */
      function isset_data($key) {
          return (isset($this->data[$key]));
      }
}