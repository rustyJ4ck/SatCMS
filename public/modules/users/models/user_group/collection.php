<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2 2010/07/21 17:57:23 surg30n Exp $
 */  
  
class user_group_collection extends model_collection {
}

class user_group_item extends model_item {

    /** @var acl_collection */
    private $_acls;

    function with_acls() {
        return core::module('users')->with_acls();
    }
    
    function load_acls() {
        $this->_acls = core::module('users')
            ->get_acl_handle()
            ->load_for_group($this->id);
        return $this->_acls;
    }
    
    function get_acls() {
        if (!$this->with_acls()) return false;
        
        if ($this->_acls === null) $this->load_acls();
        return $this->_acls;
    }
    
    function virtual_acls($t) {

        if ($t == 'edit') {
            if (!$this->get_acls()) return;
            return $this->_acls->render();
        }
    } 
    
    function modify_after($data) {
        if ($this->with_acls()) $this->update_acls(@$data['acls']);
    }
    
    function update_acls($aacls) {
        $acls = $this->get_acls();
        $acls->update_group_array($aacls, $this->id);
    }

    /*
    function prepare2edt_after(&$data) {
        $data['acls'] = $this->
    }
    */
}