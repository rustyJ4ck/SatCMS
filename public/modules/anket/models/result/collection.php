<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.1.2.1 2012/09/18 13:10:42 Vova Exp $
 */  
  
class anket_result_collection extends model_collection {

    /** @var anket_form_item */
    protected $_current_form;
    
    /** @var bool */
    protected $_notify_user = true;
    
    function set_notify_user($f) {
        $this->_notify_user = $f;
        return $this;
    }
    
    function get_notify_user() {
        return $this->_notify_user;
    }
    
    function set_current_form($f) {
        $this->_current_form = $f;
    }
    
    function get_current_form() { return $this->_current_form; }
    
}