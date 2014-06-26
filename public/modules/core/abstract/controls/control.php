<?php

/**
 *  Abs control
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: abs_control.php,v 1.3 2008/05/13 11:01:31 j4ck Exp $
 */

class model_control {
    
    private $value;
    
    /**
    * @desc 
    */
    function __construct($value = null) {
        $this->value = $value;
    }
                
    /**
    * @desc 
    */
    function create($value) {
    }
    
    /**
    * @desc 
    */
    function update($value) {
        $this->value = $value;
    }
    
    /**
    * @desc 
    */
    function remove() {        
    }
    
}
  

