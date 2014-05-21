<?php

/**
 * user blocks
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: blocks.php,v 1.2 2010/07/21 17:57:17 surg30n Exp $
 */
  
class users_blocks extends module_blocks {
    
    /**
    * predefined blocks
    */
    protected $_blocks = array(
          'user_cp'     => array('template'  => 'user_cp',      'title' => 'Панель')
        , 'online_info' => array('template'  => 'online_info',  'title' => 'Онлайн информация')
    );
     
    /**    
    * UCP Block
    * @return false 
    */
    function user_cp($params = false) {
        // operate with 'user' in template
        return false;
    }
    
    /**    
    * Users count info
    * @return false nothing
    */
    function online_info($params = false) {
        return $this->get_context()->get_controller()->online_info(true);
    }    
    
}       
