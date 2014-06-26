<?php
  
/**
 * collection
 *  
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.10.2.1 2010/08/03 06:21:05 Vladimir Exp $
 *
 */  
  

class users_collection extends model_collection {
    
        private $_url_id_field = 'id';
    
        const ANONYMOUS = 0;
    
        /**
        * User levels
        */
        private $_levels = array(     
              0   => 'anonymous'
            , 1   => 'user'
            , 50  => 'mod'
            , 100 => 'admin'        
        );
        
        /**
        * Genders
        */
        private $_genders = array(
             0 => 'unknown'
           , 1 => 'male'
           , 2 => 'femail'
        );
             
        /** domain */             
        /*              
        protected $DOMAIN = 'users';  
        */
                       
        // protected $_with_extra_fields = array('users', 'users_data');
        // protected $_with_extra_fields = array('users', 'users_extra'); // _storage_generic         
        
        /**
        * Approve switch
        */
        function toggle_active($id, $value) {  
            $this->update_item_fields($id, 
                array('active' => $value)
            );                      
        }            
        
        /**
        * Get level
        */
        function get_level_by_id($id) {
            return $this->_levels[(int)$id];
        }

        /**
        * Get level
        */
        function get_level_by_name($id) {
            $id = array_search($id, $this->_levels);
            return $id;
        }
        
        /**
        * Get gender
        */
        function get_gender_by_id($id) {
            return $this->_genders[(int)$id];
        }

        /**
        * Get gender
        */
        function get_gender_by_name($id) {
            $id = array_search($id, $this->_genders);
            return $id; //$this->_genders[$id];
        }
        
        function check_unique_nick($d, $uid = false) {
            return $this->check_unique_value('nick', $d, $uid);
        }
                
        function check_unique_login($d, $uid = false) {
            return $this->check_unique_value('login', $d, $uid);
        }
        
        function check_unique_email($d, $uid = false) {
            return $this->check_unique_value('email', $d, $uid);
        }
        
        /**
        * User register
        * @throws validator_exception
        * @return int user_id
        */
        function register_new_user($data, $level = 'user') {

            // @todo validator
            if (!core::lib('validator')->is_email($data['email'])) throw new validator_exception('email_bad');
            if (!preg_match('/^[a-z_0-9]+$/i', $data['login']))    throw new validator_exception('login_bad');
            if (empty($data['password']))                          throw new validator_exception('password_bad');
                        
            // if (!$this->check_unique_nick ($data['nick']))         throw new validator_exception('nick_exists');

            if (!$this->check_unique_login($data['login']))        throw new validator_exception('login_exists');
            if (!$this->check_unique_email($data['email']))        throw new validator_exception('email_exists');
            
            // alright, here we go
            return $this->create(
                array(
                     'nick'     => $data['nick']
                   , 'login'    => $data['login']
                   , 'email'    => $data['email']
                   , 'password' => $data['password']
                   , 'level'    => $this->get_level_by_name($level)
                )
            );            
        }
        
        
        /**
        * Check user
        */
        function check_payd_users() {
            $time = time();
            $this->db->query('UPDATE ' . $this->get_table() . ' SET payd_user = 0, payd_till = 0 WHERE payd_user AND payd_till < ' . $time);
        }
        
        function get_url_id_field() {
            return $this->_url_id_field;
        }

}