<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.10.2.1.4.3 2013/01/30 06:53:29 Vova Exp $
 */    
 
/**
CREATE TABLE `tf_users` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`login` VARCHAR( 32 ) NOT NULL ,
`password` VARCHAR( 32 ) NOT NULL
) ENGINE = MYISAM ;
*/
 
 class users_item extends abs_collection_item {
     
    /** @var acl_collection */
    protected $_acls;       
    
    /** @var user_group_item */
    protected $_group;
    
    /**
    * Urls for user
    */
    function make_urls() {
        $this->append_urls('self',      '/profile/' . $this->id . '/');
        $this->append_urls('profile',   '/profile/' . $this->id . '/');
    }

    /**
    * Update last
    */
    function last_update() {  
        if ($this->id != users_collection::ANONYMOUS) {           
            $this->last_login = time();
            $this->update_fields("last_login");
        }
    }
    
    /**
    * Get user level
    * @return string
    */
    function get_level() {
        return $this->container->get_level_by_id($this->level);
    }
    
    /**
    * Avatar test 
    */
    function render_after($data) {

      /*
      if (false !== ($data = $this->get_render_cache())) {
          return $data;
      }
      */
     
      $data['gender_string']     = $this->container->get_gender_by_id($this->gender);
      $data['gender_translated'] = core::module('users')->T('gender_' . $this->get_data('gender_string'));

      $data['level_string']      = $this->container->get_level_by_id($this->level);
      $data['level_translated']  = core::module('users')->T('level_' . $this->get_data('level_string'));
      
    }

    /**
    * Update profile
    * @todo draft 
    * @throws validator_exception
    */
    function update_profile($data) {

        // update password, if set   
        
        // remove queue if not changed        
        if ($data['nick']    == $this->nick)  unset($data['nick']); else 
            if (!$this->container->check_unique_nick ($data['nick'], $this->id))         
                throw new validator_exception('nick_exists');            
       
        if ($data['email']   == $this->email) unset($data['email']); else
            if (!core::lib('validator')->is_email($data['email'])) 
                throw new validator_exception('email_bad');  
            else            
            if (!$this->container->check_unique_email($data['email'], $this->id))        
                throw new validator_exception('email_exists');
            
        if ($data['gender']  == $this->gender)  unset($data['gender']);
        
        if (!empty($data)) {
            $this->update_fields($data);  
        
            // simulate up
            if (isset($data['gender'])) $this->gender   = $data['gender'];
            if (isset($data['nick']))   $this->nick     = $data['nick'];
            if (isset($data['email']))  $this->email    = $data['email'];
        }
    }
    
    /**
    * I am not anonymous
    */
    function is_anonymous() {
        return false;
    }
    
    /**
    * Can apload
    */
    function can_upload() {
        return !($this->is_anonymous());
    }
    

    
    /**
    * Get upload place
    * @return mixed relative path or false
    */
    function get_uploads_path($force_create = false) {
        if ($this->is_anonymous()) return false;
        $dir  = loader::DIR_UPLOADS . 'users/' . $this->login . '/';
        $full = loader::get_public() . $dir;
        if ($force_create && !is_dir($full)) {
            $this->create_upload_dir();
        }

        return $dir;
    }
    
    /**
    * Create uploads
    */
    function create_upload_dir() {
        if ($dir = $this->get_uploads_path()) {
            $dir = loader::get_public() . $dir;
            return mkdir($dir, 0770);
        }
        return false;
    }
    
    /**
    * Called from auth start
    * (current user) 
    */
    private $_interactive = false;
    
    function on_session_start() {
        $this->get_acls();
        $this->_interactive = true;
    }

    function with_acls() {
        return core::module('users')->with_acls();
    }

    function is_allow($s, $id = 0, $a = null) {   
        
        if (!$this->with_acls()) return true;
        
        if (!isset($a)) $a = acl_collection_abstract::DEFAULT_ACTION;
        
        // if not loaded
        $this->get_acls();     
        
        if (is_array($s)) {
            extract($s);
            if (!isset($id)) $id = 0;
            if (!isset($a))  $a = acl_collection_abstract::DEFAULT_ACTION;
        }
        
        if (empty($s)) return false;
        
        // admin owns all
        if ($this->level >= $this->container->get_level_by_name('admin')) return true;        
        return $this->_acls->is_allow($s, $id, $a);                    
    }
    
    function load_acls() {
        $this->_acls = core::module('users')->get_acl_handle();
            
        if ($this->gid) {
            $this->_acls->load_for_group($this->gid); //->get_objects();
        }
        
        return $this->_acls;
    }
    
    function get_acls() {
        if (!$this->with_acls()) return false;
        
        if ($this->_acls === null) $this->load_acls()->get_objects();
        return $this->_acls;
    }
    
    function virtual_acls() {
        if ($this->_interactive)
        return ($acls = $this->get_acls()) ? $acls->get_objects_simple() : false;       
    }
    
    /** @return user_group_item */    
    function get_group() {
        if (!isset($this->_group)) {
            $this->_group = $this->gid ? core::module('users')->get_managed_item('user_group', $this->gid) : false;            
        }
        return $this->_group;
    }
    
    function load_secondary($options = null) {
        $this->get_group();
        return $this;
    }
    
    function render_secondary() {
        if ($this->_group) $this->group = $this->_group->render();
        return $this;
    }      
    
}


/**
* Anonymous mock
*/

class anonymous_user extends users_item {
    
    function __construct($container) {
        
        parent::__construct($container, false);
        
        $this->nick     = 'Anonymous';
        $this->login    = 'Anonymous';
        $this->id       = users_collection::ANONYMOUS;
        $this->active   = false;
        $this->gender   = 0;
        $this->level    = 0;
        $this->gid      = 0;
        
        $this->payd_user = false;
                
        // $this->set_container($container);
    }

    /**
    * I am anonymous
    */
    function is_anonymous() {
        return true;
    }            
    
}