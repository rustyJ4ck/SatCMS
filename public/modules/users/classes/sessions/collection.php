<?php
  
/**
 * Sessions
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.3.2.4.2.2 2012/09/11 19:23:23 j4ck Exp $
 */

class sessions_collection extends abs_collection {
    
    private $with_browser   = true;
    private $ip_octets      = 4;
    private $salt           = 'howmuchfish';
    
    private $_cleanup_sessions_max = 500;
    private $_cleanup_interval = 604800;   // ttl=1 x week
    
    private   $_session_key = '';
                               
    protected $fields = array(
          'id'              => array('type'     => 'numeric')
        , 'uip'             => array('type'     => 'numeric', 'unsigned' => true, 'long' => true )
        , 'uid'             => array('type'     => 'numeric')
        , 'last_update'     => array('type'     => 'unixtime',  'default' => 'now')
        , 'skey'            => array('type'     => 'text', 'size' => 32)
        , 'sid'             => array('type'     => 'text', 'size' => 32)
        , 'sdata'           => array('type'     => 'text', 'no_format' => true)         // serialized
    );
    
   
    /**
    * Make sessions collection
    */
    function construct_after() {          
        $this->update_session_key();
    }
    
    protected function update_session_key() {
        $this->_session_key = $this->make_key();                    
    }
    
    /**
    * @return sessions_collection
    */
    function with_user_agent($f = null) {
        if (isset($f)) {
            $this->with_browser = $f;
            $this->update_session_key();
            return $this;
        }
        return $this->with_browser;
    }

    /**
     * @return dummy_sessions_item
     */
    function create_dummy_session() {
        $item = new dummy_sessions_item($this);
        $item->id  = false;
        $item->uid = false;
        $item->uip = false;
        $this->append($item, -1);
        return $item;
    }


    /**
    * Create new
    */
    function create_new($uid, $uip) {
        
        $sid = $this->make_sid();
        
        $id = $this->create(
            array(
                'uid' => $uid
              , 'uip' => $uip
              , 'sid' => $sid
            )
        );

        return $this->get_item_by_id($id);
    }
    
    /**
    * Get current session (by sid)
    * @param string sid
    * @return session|false
    */
    function get_session($id) {
        if (empty($id)) return false;
        
        return
        $this->clear()
            ->set_where("sid = '%s' AND skey = '%s'"
            , $this->get_db()->escape($id)
            , $this->_session_key
        )->set_limit(1)
         ->disable_order()
         ->load()
         ->get_item();
    }
        
    /**
    * Make session key
    */
    function make_key() {
        $key = $this->salt;
        if ($this->with_browser) {
            $key .= (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        }
        if ($this->ip_octets && isset($_SERVER['REMOTE_ADDR'])) {
            $num_blocks = ($this->ip_octets > 4) ? $this->ip_octets : 4;
            $blocks = explode('.', $_SERVER['REMOTE_ADDR']);
            for ($i = 0; $i < $num_blocks; $i++) {
                $key .= $blocks[$i] . '.';
            }
        }
        return bin2hex(md5($key, true));
    }
    
    function get_session_key() {
        return $this->_session_key;
    }
    
    /**
    * Make sid
    */
    function make_sid() {
        $key = microtime(true);
        return bin2hex(md5($key, true));         
    }
    
    
    /**
    * Make some dust clean
    * Caution! Its release current items and settings
    */
    function clean_sessions() {
        $this->cleanup();
        $this->fix_overload();        
    }
    
    /**
    * Deletes old entries
    * one week
    * @todo cleanup if too many entries
    */
    function cleanup() {
        $time = (time() - $this->_cleanup_interval);
        core::dprint(array("[sessions] clean older entries : %s", date('d.m.Y H:i', $time)));     
        
        $this->clear(true) 
            ->set_where("last_update < %d", $time)
            ->remove_all_fast();
    } 
    
    function get_expire_time() {
        return $this->_cleanup_interval;
    }
    
    /**
    * Get obsolete data
    */
    function fix_older($time = null) {
        
        //@todo see self::clean
        return;
        
        core::dprint(array("[sessions] fix_older %s", date('d.m.Y H:i', $time)));   
        $this->clear(true);
        $this->is_delayed(true);   
        $this->set_where('last_update < %d', $time);
        $this->load();
        $this->remove_all();
    }
    
    /**
    * Test for maximum records
    * @todo fix
    */
    function fix_overload($max = null) {
        
        $max = $max ? $max : $this->_cleanup_sessions_max;
        
        // core::dprint("[sessions] fix_overload broken!");
            
        $this->clear(true);
        $count = $this->count_sql();    
           
        if ($count <= $max) {
            core::dprint(array("[sessions] fix_overload %d <= %d, skip cleanup", $count, $max));   
            return false;
        } 
        
        $max = $count - $max;
        
        core::dprint(array("[sessions] clean %d entries", $max));   
        
        if ($count > $max) {
            // @todo clean sql, make thru orm
            $max = $count - $max;
            $sql = "DELETE LOW_PRIORITY FROM " . $this->get_table() . " ORDER BY last_update ASC LIMIT " . $max;
            $this->get_db()->sql_query($sql);
        }
    }
}
