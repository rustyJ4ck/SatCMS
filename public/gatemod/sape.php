<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: sape.php,v 1.1.2.2 2011/05/10 17:18:35 surg30n Exp $
 */

require_once 'generic.php';  

class sape_gatemod extends generic_gatemod {
    
    private $_sape;
    private $_sape_user;
    private $_selector = '<!--CHOCOLATESTARFISH-->';
    
    function construct_after() {
        
        $this->_sape_user = @$this->_config['sape_user'];
        
        if (!$this->_sape_user) {
            $this->enabled = false;
            return;
        }
        
        define('_SAPE_USER', $this->_sape_user);
        require_once $this->_sape_user . '/sape.php';
        $this->_sape = new SAPE_client(array('charset' => 'UTF-8', 'multi_site' => true/*, 'force_show_code' => true*/));         
    }
    
    function run(&$content) {
        if (!$this->enabled) return;
        $links = $this->_sape->return_links();
        $content = preg_replace('@' . $this->_selector . '@', $links, $content);
    }
}

