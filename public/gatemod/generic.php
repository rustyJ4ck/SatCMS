<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: generic.php,v 1.1.2.1 2011/05/06 09:03:53 Vladimir Exp $
 */
  
class generic_gatemod {
    
    protected $_config;
    protected $enabled = true;
    
    function __construct($config = null) {
        $this->_config = $config;
        $this->construct_after();
    }
    
    function construct_after() {
    }
    
    function run(&$content) {
    }                                
}