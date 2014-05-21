<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2 2010/07/21 17:57:20 surg30n Exp $
 */  
  
class bans_collection extends abs_collection {

       protected $fields = array(
              'id'               => array('type' => 'numeric')
            , 'time'             => array('type' => 'unixtime', 'default' => 'now')
            , 'till'             => array('type' => 'numeric')
            
            , 'c_count'          => array('type' => 'numeric',  'default' => 1)
            
            , 'comment'          => array('type' => 'text')                   
            , 'ip'               => array('type' => 'text') 
            
            , 'active'           => array('type' => 'virtual')
            , 'ip_string'        => array('type' => 'virtual')
       );  
       

        
    function __construct($config) {    
        $config['order_sql'] = 'time DESC';
        return parent::__construct($config);
    }
       
    /**
    * Get obsolete data
    */
    function fix_older($time) {
        $this->clear(true);
        $this->is_delayed(true);
        $this->set_where('(time + till) < %d', $time);
        $this->load();
        $this->remove_all();
    }
    
    /**
    * Test for maximum records
    */
    function fix_overload($max) {
        $this->clear(true);
        $count = $this->count_sql();    
        $max = $max ? $max : 1024;
        if ($count > $max) {
            $max = $count - $max;
            $sql = "DELETE LOW_PRIORITY FROM " . $this->get_table() . " ORDER BY time ASC LIMIT " . $max;
            $this->get_db()->sql_query($sql);
        }
    }
    
    /**
    * Get by IP
    */
    function get_by_ip($ip) {
        $ip = ip2long($ip);
        $this->clear();
        $this->set_where('ip = %d', $ip);
        $this->load();
        return $this->get_item();
    }
    
    /**
    * Ban it
    * @param string IP
    * @param integer time in seconds
    * @param string comment
    */
    function ban_ip($ip, $till = 300, $comment = '') {
        
        $already_ban = $this->get_by_ip($ip);
        
        if ($already_ban) {
            // increment ban time if alreay banned
            $already_ban->till = $till * $already_ban->c_count;
            $already_ban->time = time();
            $already_ban->c_count += 1;
            $this->is_delayed(true);            
            $already_ban->save();
            return;            
        }
        
        $ip = ip2long($ip);
        
        if ($this->is_empty()) {
            $this->is_delayed(true);
            $this->create(array(
                  'till'        => $till    
                , 'comment'     => $comment
                , 'ip'          => $ip
            ));
        }
    }
    
    /**
    * Check spammer
    * @return bool true if spammer
    */
    function check_spam($uri, $ip = false) {
        
        $ip = !empty($ip) ? $ip : $_SERVER['REMOTE_ADDR'];
        
        if (empty($ip)) return false;
        
        if (strpos($uri, '?') !== false && strpos($uri, '://') !== false) {
            $this->ban_ip($ip, 500, 'BAD URL : ' . $uri);
            core::dprint("You are banned for 500* sec via " . $uri);
            return true;
        }
        
        // check in base
        if ($ban = $this->get_by_ip($ip)) {
            if (($ban->time + $ban->till) > time())
                return true;
        }
        
        return false;
    }

}