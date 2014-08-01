<?php

/**
 * Users
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: module.php,v 1.9.6.5 2012/10/25 09:52:43 Vova Exp $
 */
  
class tf_users extends core_module {  

    protected $editor_default_action = 'users';

    /** @var  users_collection */
    private $users;
    
    /**
    * anonymous user 
    */
    private $anonymous;
    
    /** 
    * control panel data for template @see self::set_cp_data
    * cp.urls  - control urls (login/logout)
    * cp.links - cp options fot logged user
    */    
    private $cp_data;
    
    /**
    * Control panel links
    */
    private $_cp_links; 

    /**
    * Creator 
    */
    function construct_after() {
        core::dprint('[users] construct', core::E_DEBUG0);
        $this->users = $this->model('users');
    }

    // model handles
        
    /** @return users_collection        */  function get_users_handle()         {  return $this->model('users');              }
    /** @return user_group_collection   */  function get_user_group_handle()    {  return $this->model('user_group');         }
    /** @return sessions_collection     */  function get_sessions_handle()      {  return $this->model('sessions');           }
    /** @return acl_collection          */  function get_acl_handle()           {  return $this->model('acl');                }
    /** @return users_payments_collection*/ function get_users_payments_handle(){  return $this->model('users_payments');     }

    /** @param sessions_item $session */
    function on_auth_session($session) {

        // fix session with no token
        if (!$session->token) {
            $session->update_token();
        }
    }

    /**
     * Check csrf
     * @throws router_exception
     */
    function on_route_before() {
      $this->check_forged();
    }

    function check_forged() {
        if ($this->request->has_post() && $this->request->forged()) {
            throw new controller_exception('Forged request', 401);
        }
    }

    function with_acls() {
        return $this->config->get('acls', 0);
    }

    /**
    * init0 
    */
    function init0() {
        core::dprint('[users] init0');

        // link
        // $this->auth = core::lib('auth');
        
        $return = parent::init0();
        $this->_cp_links = array();
        
        // lookup links array
        $this->_cp_links = $this->load_cp_links();

        // kick acls
        if (core::in_editor() && $this->with_acls()) {
            $this->get_acl_handle();
        }
            
        /**
        * Links for logged users
        */
        if ($this->auth->logged_in()) {
            $admin = ('admin' == $this->get_current_user()->get_level());
            if ($admin)
                $this->_cp_links['admin'] =  array('title' => $this->T('cp_admin')      , 'url' => '/editor/');
                
            /*
            if ($this->get_current_user()->level >= 50)
                $this->_cp_links['mod'] =  array('title' => $this->translate('cp_mod')      , 'url' => '/users/cp/mod/');  
            */    
        //    $this->_cp_links['pms'] =  array('title' => $this->translate('cp_pms')      , 'url' => '/users/cp/pms/');   
        }
        
        return $return;
    }
    
    /**
    * Завершающий init 9
    */
    public function init9() {
        core::dprint('[users/shutdown]');
        return parent::init9(); 
    }                  
    
    /**
    * Set control panel data
    */
    public function set_cp_data($key, $data) {
        $this->cp_data[$key] = $data;
    }
    
    /**
    * get cp links
    */
    public function get_cp_links($id = null) {
        return ($id) ? $this->_cp_links[$id] : $this->_cp_links;    
    }
    
    /**
    * lookup usercp links
    */
    function load_cp_links($cp_links_file = null) {
        
        $cp_links_file = $cp_links_file
            ? $cp_links_file
            : ($this->root_dir . 'cp.actions.php');
            
        if (file_exists($cp_links_file))
            return require $cp_links_file;        
            
        return array();
    }
    
    function get_default_cp_option() {
        if (!empty($this->_cp_links)) 
        foreach ($this->_cp_links as $k => $v) {
            if (!empty($v['default'])) return $k;            
        }
        return false;        
    }
    
    /**
    * Render module
    * (indirect)
    */
    public function render() {

        // if ($this->auth->logged_in()) {
        $this->set_cp_data('links', $this->get_cp_links());
        // }
        
        // urls for user
        $this->set_cp_data('urls', array(
             'register'       => array('title' => $this->T('has_no_account')    , 'url' => '/users/register/')
           , 'login'          => array('title' => $this->T('login')             , 'url' => '/users/login/')
           , 'logout'         => array('title' => $this->T('logout')            , 'url' => '/users/logout/')
           , 'lost_password'  => array('title' => $this->T('lost_my_pass')      , 'url' => '/users/lost_password/')
           , 'cp'             => array('title' => $this->T('cp_title')          , 'url' => '/users/cp/')
        ));
        
        // to template
        $this->renderer->set_data('cp', $this->cp_data);
    }
    
    /**
    * Get anonymous user
    * @return users_item
    */
    public function get_anonymous_user() {
        return $this->anonymous ?: ($this->anonymous = new anonymous_user($this->users));
    }
 
    /**
    * Get user by id
    */
    function get_user($id, $prop = 'id') { 
        
        if (!$id && 'id' == $prop) {
            return $this->get_anonymous_user();  
        }
        
        $user = $this->manager->get($this->users->get_class(), $prop . '_' . $id);
         
        if ($user) {
            return $user;
        }
        
        if ($prop == 'id') {
            $user = $this->users->load_only_id($id);
        }
        else {
            $user = $this->users->clear()->where($prop, $id)->load_first();
        }
        
        // if user not found, make him anonymous

        if (!$user) {
            $user = $this->get_anonymous_user();  
        }

        $this->manager->set($this->users->get_class(), $prop . '_' . $id, $user);
        
        return $user;
    }
    
    /**
    * get users
    */          
    function get_users() {
        return $this->users;
    }
        
    /**
    * Get payments for user
    * @return users_payments_collection collection
    */
    function get_users_payments($uid) { 
        $data = $this->get_users_payments_handle();
        $data->set_where('uid = %d', (int)$uid);
        $data->load();
        return $data;
    }
    
    /**
    * Get user logged in system
    */
    function get_current_user() {
        return $this->auth->get_user();
    }
    
    /**
    * Cp update profile
    */
    function update_user_profile($post) {
        // nick, email, password, gender
        // @todo validate
        $data = array();
        if (!empty($post['password'])) $data['password'] = $post['password'];
        $data['nick']   = $post['nick'];        
        $data['email']  = $post['email'];        
        $data['gender'] = (int)$post['gender'];        
        $this->get_current_user()->update_profile($data);
        return true;
    }


    /**
    * Cleanup (called on login)
    */
    function clean_sessions() {
        
        $time = time();
        
        $last_time    = (int)core::cfg('users_crontab_last');
        $interval     = (int)core::cfg('users_crontab_interval',        600);
       
        if (empty($last_time) || ($last_time + $interval) < $time) {
            
            /* do the job:
              - clear obsolete sessions
            */
            
            $this->get_sessions_handle()->clean_sessions();
                               
            $this->core
                ->get_dyn_config()
                ->update_param('users_crontab_last', $time);
        }                     
    }
    
    /**
    * Events
    * ------
    */
    

    
    /**
    * Count all users
    */
    function count_all() {
        $ses = $this->get_users_handle();       
        $count = $ses->count_sql();
        return $count;
    }                  
    
    /**
    * Count online
    */
    function count_online() {
        $ses = $this->get_sessions_handle();
        $time = time() - 900;
        $ses->set_where('last_update > %d', $time);
        $count = $ses->count_sql();
        return $count;
    }
    
    /**
    * Count online logged in
    */
    function count_online_logged() {
        $ses = $this->get_sessions_handle();
        $time = time() - 900;
        $ses->set_where('last_update > %d AND uid > 0', $time);
        $count = $ses->count_sql();
        return $count;
    }
    
    /**
    * On posts view
    */
    function on_post_view($post) {
        $u = $this->get_current_user();
        if (!$u->is_anonymous() && $u->id != $post->owner_id) {
            core::module('content')->get_post_views_handle()->post_view($post, $u);
        }
    }
    
}
