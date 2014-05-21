<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: test.php,v 1.1.2.1 2011/05/10 16:10:16 surg30n Exp $
 */
 
require_once 'generic.php';  
  
class test_gatemod extends generic_gatemod {
    
    protected $_config;
    protected $enabled = true;
    
    function __construct($config = null) {
        $this->_config = $config;
        $this->construct_after();
    }
    
    function construct_after() {
    }
    
    function run(&$content) {
        $content = preg_replace('@машины@u', '@МАШИНЫ!!!@', $content);
    }                                
}