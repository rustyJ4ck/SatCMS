<?php
  
/**
 * Authorization lib
 * Use users module
 * 
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: auth.php,v 1.6.2.3.2.8 2012/11/02 08:49:15 Vova Exp $
 */
 
 class tf_auth {
     
     private $_anonymous_id  = 0;
     
     private $_cookie_domain;
     private $_cookie_name   = 'vidz0xoid';
     private $_cookie_expire = 2678400 /*month 50400*/;
     private $_update_interval = 300;
     
     private $_cookie_httponly = true;
     private $_secured = false;                        // ssl
     
     // handles
     private $users;
     private $sessions; 
     // items
     private $user;
     private $mod_users;

     /** @var  sessions_item */
     private $session;
     
     private $_logged_in = false;
     
     private $_session_id = false;
     
     private $_disable_auth  = false;
     
     private $_crawlers = 'Yandex|Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';

     private $_autologin_UID;

     
     /**
     * Create auth lib
     * @param users_collection users handle
     * @param bool disable auth
     */
     function __construct(tf_users $musers, $disable_auth = false) {

         $core = core::selfie();
                  
         $this->_cookie_domain = '.' . @$_SERVER['HTTP_HOST']; //core::get_instance()->get_main_domain();

         $this->mod_users   = $musers;
         $this->users       = $musers->get_users_handle();
         $this->sessions    = $musers->get_sessions_handle()->with_user_agent(
            $core->cfg('auth.with_browser', true)
         );
         
         $this->_cookie_httponly = $core->cfg('auth.cookie_httponly', false);

         $this->_autologin_UID = $this->mod_users->cfg('autologin_UID', 0);

         $this->_disable_auth  = $disable_auth;
         
         if ($this->is_crawler()) {
             $this->_disable_auth = true;
         }
         
         if ($this->_disable_auth) core::dprint('[AUTH] Sessions disabled');
         
         $this->onload();
     }

     /**
      * Called on system shutdown @see core::init10()
      */
     function on_session_end() {
         if ($this->_disable_auth) return;
         if ($this->session) $this->session->on_session_end();
     }

     
     /**
     * Users logged in
     */
     public function logged_in() {
         return $this->_logged_in;
     }
     
     /**
     * Page load
     * Check for sessions
     */
     public function onload() {
         
         if ($this->_disable_auth) {
             $this->set_null_session();
             return;
         }

         if ($this->_autologin_UID) {
             $this->set_uid_session($this->_autologin_UID);
             return;
         }
         
         $session = $this->get_session();
         
         if (!$session) {
             core::dprint('cant create or load session', core::E_CRIT);
             $this->set_session(false);
             return;
         }
         
          core::dprint(array('[AUTH] SID#%s UID#%d, valid till %s'
            , $this->_session_id
            , $this->user->id
            , $this->session ? date('d.m.Y H:i', $this->session->get_expire_time()) : '-'
            ));
          core::dprint(array('[AUTH] USER UID#%d IP#%s %sUA#%s'
            , ($session ? $session->uid : 0)
            , $this->get_user_ip()
            , ($this->sessions->with_user_agent() ? '+' : '-')
            , $this->get_ua()
            ));
     }
     
     /**
     * Find/Create session
     */
     private function get_session() {
         
         $session_id = isset($_COOKIE[$this->_cookie_name]) ? $_COOKIE[$this->_cookie_name] : false;
         
         if ($session_id && ($test_session = $this->sessions->get_session($session_id))) {    
            // ok, found one                            
            $this->set_session($test_session);
            $this->update_session();                
         }
         else {
            // otherwise (wrong session or session not exists)
            $test_session = $this->create_session();
         }
         
         return $test_session;
     }
     
     /**
     * @return sessions_item
     */
     function get_current_session() {
        return $this->session;
     }

     /**
     * Create new session
     * and set it
     */                  
     public function create_session() {

         $this->set_session(
            ($tmp_session = $this->sessions->create_new($this->_anonymous_id, $this->get_user_ip(true)))
         );
         
         $this->update_cookies();
         
         return $tmp_session;            
     }
     
     /**
     * Update cookie session
     */
     private function update_cookies() {

         if (headers_sent()) {
             // something goes wrong, fix? sessions overflow
             core::dprint('[AUTH] Headers already sent', core::E_ERROR);
             return false;
         }

         return setcookie(
              $this->_cookie_name 
            , $this->_session_id 
            , (time() + $this->_cookie_expire)
            , '/'
            , $this->_cookie_domain
            , $this->_secured
            , $this->_cookie_httponly
            );  
     }
     
     /**
     * Sets current session
     * call in @see self::get_session() when found a cookie
     */
     public function set_session($session) {
         
         // database fail
         if (!$session) {
            $this->_session_id = 0;
            $this->set_user(0);
            return false;
         }
         
         $this->session = $session;
         $this->_session_id = $session->get_sid();
         $this->set_user($session->uid);         
     }

     /**
      * Set empty session
      * @return $this
      */
     function set_null_session() {
         $session = $this->sessions->create_dummy_session();
         $this->set_session($session);
         return $this;
     }

     /**
      * Create fake session with UID
      * @param $uid
      * @return $this
      */
     function set_uid_session($uid) {
         core::dprint(array(__METHOD__ . ' ID:%d', $uid), core::E_INFO);
         $session = $this->sessions->create_dummy_session();
         $session->uid = $uid;
         $this->set_session($session);
         return $this;
     }

     /**
     * Update session and user
     * last activity timestamp 
     */
     /*private*/ function update_session($force = false) {
         if ($force || ($this->session->last_update + $this->_update_interval < time())) {  
            $this->session->last_update();
            $this->user->last_update();
            $this->update_cookies(); 
         }
     }
     
     /**
     * Sets logged user (by id)
     * @param integer UID
     * @throws auth_exception
     */
     public function set_user($uid) {            
         
         if (!empty($uid)) {
             $this->user = $this->mod_users->get_user((int)$uid);

             if ($this->user) {
                $this->_logged_in = true;
                return;
             }
             else throw new auth_exception('No User for UID ' . $uid);
         }
         
         $this->user = core::module('users')->get_anonymous_user();
     }  

     /**
     * Get current user
     * @return user_item user object
     */
     public function get_user() {
         return $this->user;
     }  

     /**
     * Try to login
     * @return bool
     */
     public function login($login, $password, $keep = false) {
         $user = $this->users->clear()
            ->set_where("login = '%s' AND password = '%s' AND active", $login, $password)
            ->set_limit(1)
            ->load()
            ->get_item(); 
                  
         if ($user) {
             // we are in system
             $this->user = $user;
             $this->update_session_uid();
             $this->_logged_in = true; 
             core::event('login', $user);
             return true;
         }
         return false;
     }
     
     /**
     * Out
     */
     public function logout() {
         if (!$this->_logged_in) return false;
         core::event('logout', $this->get_user());
         $this->set_user(0);
         $this->_logged_in = false;
         $this->update_session_uid(); 
         return true;
     }
     
     /**
     * Update session uid when user logged in
     */
     private function update_session_uid() {
         
         if (!$this->session) {
             throw new auth_exception('update_session_uid when no/disabled session');             
         }
         
         $this->session->update_uid((int)$this->user->id);         
     } 
     
     /**
     * To template
     */
     public function render() {
         $data = $this->user->render();
         $data['logged_in']   = $this->_logged_in;
         $data['useragent']   = $this->get_ua();
         $data['session_sid'] = $this->_session_id;
         return $data;
     }
     
     // helpers

     function is_crawler() {
        return preg_match('@' . $this->_crawlers. '@', $this->get_ua());
     }
     
     /**
     * Get IP   
     */
     public function get_user_ip($enc = false) {
         $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.2';
         return $enc ? ip2long($ip) : $ip;
     }
     
     function get_ua() {
         return @$_SERVER['HTTP_USER_AGENT'];
     }

 }
