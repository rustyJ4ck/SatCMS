<?php
       
/**
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: output_filter.php,v 1.2 2010/07/21 17:57:16 surg30n Exp $
 */    

/**
*   Core output filter
*/   
abstract class output_filter {
    
    private $core;
    private $name;
    
    /**
    * Creator
    */
    function __construct($core) {
        $this->core = $core;
        $this->activate();
    }
    
    /**
    * Activate filter
    */
    abstract function activate();  
    
    /**
    * Begin output event               
    */
    abstract function on_output_begin();
    
    /**
    * Finish output
    * @param string buffered output
    * @return string output, in any
    */
    abstract function on_output_finish($output);    
    
    /**
    * Get filter name
    */
    function get_name() {
        return $this->name;
    }
    
}
