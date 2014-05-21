<?php

/**
 * Sape interface
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: sape.php,v 1.2 2009/08/05 08:45:21 surg30n Exp $
 */

$_su = core::get_instance()->get_cfg_var('sape_user');

if (!empty($_su)) {      
    if (!defined('_SAPE_USER')) define (_SAPE_USER, $_su);    
    require_once loader::get_public($_su . '/sape.php');
}

if (!class_exists('SAPE_client')) {
    class SAPE_client {
        function SAPE_client ($p = array()) {
            // mock
            core::dprint('[LIB_SAPE] Using mock', core::E_ERROR);
        }
    }
}

class sape extends SAPE_client {
    
    function __construct() {
       parent::SAPE_client(array('charset' => 'UTF-8'));
    }
    
    public function is_enabled() {
        return defined('_SAPE_USER');
    }
}



