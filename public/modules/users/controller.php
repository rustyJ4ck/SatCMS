<?php

/**
 * User contoller
 * 
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: controller.php,v 1.7.2.1.4.9 2012/09/18 07:03:30 Vova Exp $
 */
 
/**
* @package users
*/

class users_controller extends module_controller {

    protected $_cp_url = '/users/cp/';
    protected $_editor_url = '/editor/';
    
    /** @var tf_auth */
    protected $_auth;
    
    function construct_after() {
        $this->_auth = core::lib('auth');
    }

    function section_api() {
    }

    function action_api_user_current() {

        $this->renderer
            ->set_ajax_answer(
                array_merge(
                    $this->context->get_current_user()->render(
                        array('id', 'login', 'email', 'level', 'active')
                    ),
                    array('logged' => $this->_auth->logged_in()))
            )
            ->ajax_flush();

    }

    
    /*
    core::get_instance()->set_message('invalid');
    core::get_instance()->set_message_data($_POST, false);
    */

    /**
     * Legacy user actions
     */

    /**
    * Run
    * PHP5.4: Declaration of users_controller::run() should be compatible with front_controller::run($route, $params) : 2048
    * @return template
    */
    public function run($r, $params = null) {

        // base routes
        if ($this->router->get_current_route()) {
            return parent::run($r, $params);
        }
        
        $this->set_section_name('users');
        
        // default action
        if (empty($r->action)) $r->action = 'users';

        $this->set_req($r);
        
        if (!is_callable(array($this, $r->action))) {
            throw new controller_exception('No such action', router_exception::ERROR);
        }

        // call method
        core::dprint('users_controller::' . $r->action);
        call_user_func(array($this, $r->action), $r);
        return $this->get_template();
    }
    
    /**
    * Suggest
    */
    function suggestions() {
        core::var_dump('suggest');
        //$this->get_context()->get_users_like()
    }
    
    /**
    * Bans list
    */
    function bans() {
        $data = $this->get_context()->render_bans_list();
        $this->get_renderer()->set_filtered_list('bans', $data); 
        $this->get_renderer()->set_page_title($this->get_context()->T('bans'));
        $this->set_template('bans_list');  
    }
    
    /**
    * Users list
    */
    public function users() {
        $data = $this->get_context()->render_users_list();
        $this->get_renderer()->set_filtered_list('users', $data);
        $this->get_renderer()->set_page_title($this->get_context()->T('users_list'));
        $this->set_template('users/list');        
    }    
    
    /**
    * Online users list
    * @param bool true if needs return data (from block call)
    */
    public function online_info($return = false) {
        $this->set_template('blocks/online_info');
        
        $data = array();
        
        $data['all']    = $this->get_context()->count_all();
        $data['online'] = $this->get_context()->count_online();
        $data['logged'] = $this->get_context()->count_online_logged();        

        if (!$return) {
            $this->get_renderer()->set_return($data);        
            
            if (loader::in_ajax())
                $this->get_renderer()->set_ajax_answer($data);
        }
        else return $data;
    }
    
    public function test() {
        $this->set_template('users/test');
        return  true;        
    }
    
    /**
    * Login
    */
    public function login() {
        
        if (!loader::in_ajax()) {
            $this->set_template('users/login');
            return;
        }
        
        
        // cleanup
        $this->get_context()->clean_sessions();
        
        $redirect = functions::request_var('redirect', '');
        $login    = functions::request_var('login', '');
        $password = functions::request_var('password', '');
        
        // already logged
        if ($this->_auth->logged_in()) {
                $this->set_null_template();
                core::lib('renderer')->set_ajax_answer(array(
                      'status'  => false
                    , 'message' => $this->get_context()->T('already_logged')
                ));
                return;        
        }
        
        // empty fields
        if (empty($login) || empty($password)) {             
                $this->set_null_template();
                core::lib('renderer')->set_ajax_answer(array(
                      'status'  => false
                    , 'message' => $this->get_context()->T('empty_login')
                ));
                return;
        }
        
        // try
        $result = $this->_auth->login($login, $password);
        
        if ($result) {
                $message = 'done';
                $status  = true;
                $this->set_template('blocks/user_cp');                
                
                $user = $this->_auth->get_user();
                
                if (empty($redirect)) {
                    if ($user->level >= 100) 
                        $redirect = $this->_editor_url;
                    else 
                        $redirect = $this->_cp_url;
                }
                
                core::lib('renderer')->render_user();
                
                
        }
        else {
                $status = false;
                $this->set_null_template();
                $message = $this->get_context()->T('fail_to_login');
        }
               
        core::lib('renderer')->set_ajax_answer(array(
              'status'  => $status
            , 'message' => $message
            , 'redirect'=> $redirect
        ));      
      
    }
    
    /**
    * Logout
    */
    public function logout() {
        
        if ($this->_auth->logged_in()) {
            $this->_auth->logout();    
        }
        
        if (loader::in_ajax()) {
            $this->set_null_template(); 
            core::lib('renderer')->set_ajax_answer(array());            
        }
        else {
            core::get_instance()->set_message(array('users','user_logouted'));
        }
    }
    
    /**
    * Lost my pass
    */
    public function lost_password() {  
        $this->set_template('user_lost_password');     
    }
    
    /**
    * Run profile
    */
    public function profile($r) {

        $user = $this->get_context()->get_user($r->user, 'id');
        core::lib('renderer')->set_main_title($this->get_context()->T('user_info'));
        
        // user not found, throw some shit about
        if ($user->id == 0) {
            throw new controller_exception('No such user');
        }
        
        core::lib('renderer')->set_data('user_view', $user->render());
        $this->set_template('users/profile');
    }
    
    /**
    * Register
    */
    public function register($r) {
        $this->set_template('user_register');
        $data = $_POST;
        
        $error = false;
        
        $op = functions::request_var('op');
        
        // valid only thru ajax
        if (!loader::in_ajax()) return;
        
        // register new
        if ($op == 'register') {
            try {
                $uid = $this->get_context()->get_users_handle()->register_new_user($data);
            }
            catch (validator_exception $e) {
                    $error = $e->getMessage();
                    $error = $this->get_context()->T($error);
            }                                     
            

            // core::var_dump();
            
            if (false === $error) {
                // log me in!
                $login    = functions::request_var('login', '');
                $password = functions::request_var('password', '');                
                if (!empty($login) && !empty($password))
                $result = $this->_auth->login($login, $password);   
            }           
               
            // log user in!
            
            if (loader::in_ajax()) {
                $this->set_null_template();
                core::lib('renderer')->set_ajax_answer(array(
                      'status'  => ($error === false)
                    , 'message' => $error
                    , 'url'     => $this->get_context()->get_router()->make_url('users/register_success/')));

            }
        }
    }
    
    /**
    * Register success
    */
    public function register_success($r) {
        $this->set_template('user_register_success'); 
    }
    
    /**
    * Control panel
    * -------------
    */
    
    /** cp user, accessible thru @seeself::get_user() */
    private $_cp_user;
    
    /** 
    * get user for cp 
    */
    function get_user() {
        return $this->_cp_user;
    }
    
    /**
    * CP Entry
    * /users/cp/../
    * @param array
    *   $r->option_params' => array(0=>,1=>)
    * @throws router_exception
    * @throws controller_exception
    */
    function cp($r) {

        // disable cache
        functions::headers_no_cache();
        
        // force login screen
        if (!$this->_auth->logged_in()) {
            /*
            $this->renderer->set_message($this->get_context()->T('Please login'));
            */
            $this->renderer->set_return('redirect', $_SERVER['REQUEST_URI']);
            $this->set_template('users/login');
            return;
        }
        
        $this->_cp_user = $this->get_context()->get_current_user();
        
        $this->set_section_name('cp');
        $this->set_action_name('cp');
        $this->set_template('users/cp');
        
        if (empty($this->req->option) && ($default_opt = $this->get_context()->get_default_cp_option())) {
            $this->req->option = $default_opt;    
        }
        
        if (!empty($this->req->option)) {
            $cmd = 'cp_' . $this->req->option;
            
            if (!is_callable(array($this, $cmd)))
                throw new router_exception('Bad action', router_exception::NOT_FOUND);
            
            // call cp method
            call_user_func(array($this, $cmd));
            
            $this->get_context()->set_cp_data('option', $this->req->option);
        }
        
    }
   
    /**
    * Cp action
    */
    function cp_profile() {
        $post = core::lib('request')->get_post();
        
        // change nick, email, gender, password
        if ('update_profile' == core::get_params('op')) {
                        
            $error = false;
            try {
                $this->get_context()->update_user_profile($post);
            }
                catch (validator_exception $e) {
                    $error = $e->getMessage();
                    $error = $this->get_context()->T($error);
            } 
         
            $message = ($error !== false) ? $error : $this->get_context()->T('profile_update_ok');

            if (loader::in_ajax()) {
                $this->set_null_template();
                core::lib('renderer')->set_ajax_answer(array('status' => (false === $error), 'message' => $message));
            }   
            else {
                $core = core::get_instance();
                $core->set_raw_message($message);
                $core->set_message_data(false, (false !== $error));
            }
        }
        
    }

    /**
    * Cp::avatar
    */
    function cp_avatar() {
        require 'modules/users/actions/cp/avatar' . loader::DOT_PHP;
    }    
    
    /**
    * Cp action
    */
    function cp_balance() {
        require 'modules/users/actions/cp/balance' . loader::DOT_PHP;
    }
    
    /**
    * Cp action
    */
    function cp_payments() {
        require 'modules/users/actions/cp/payments' . loader::DOT_PHP;
    }
    
    /**
    * Cp action
    */
    function cp_my_views() {
        require 'modules/users/actions/cp/my_views' . loader::DOT_PHP;      
    }
    
    /**
    * Cp action
    */
    function cp_post() {
        require 'modules/users/actions/cp/post' . loader::DOT_PHP;
    }   
    
    /**
    * Cp my posts
    */
    function cp_my_posts() {
        require 'modules/users/actions/cp/my_posts' . loader::DOT_PHP;
    }       

    /**
    * Cp my bugs
    */
    function cp_my_bugs() {
        require 'modules/users/actions/cp/my_bugs' . loader::DOT_PHP;
    }      
    
    /**
    * Cp action
    */
    function cp_config() {
    }             

    /**
    * Cp vip
    */
    function cp_vip() {
        require 'modules/users/actions/cp/vip' . loader::DOT_PHP;     
    } 
    
    /**
    * Cp mod
    */
    function cp_mod() {
        require 'modules/users/actions/cp/mod' . loader::DOT_PHP;     
    }  
    
    /**
    * PMS
    */
    function cp_pms() {
        require 'modules/users/actions/cp/pms' . loader::DOT_PHP;
    }      
       
}
