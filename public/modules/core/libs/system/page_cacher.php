<?php
  
/**
* @package core-libs
* @version $Id: page_cacher.php,v 1.2.8.1 2012/06/09 08:52:47 Vova Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/  
  
/**
* Page cacher
*   
* Flow:
* core::run()
*   get_cache_page(uri)
*       ?true: exit
* 
* core::shutdown()
*   cache_page(uri, user, content)
*/

class tf_page_cacher {
    
    private $handle;
    private $cache;
    
    /**
    * Threshold table
    * Harder rules must be placed top
    * hits %d per %d secs cached for %d secs
    */
    private $_threshold_table = array(
         array('hits' => 3,  'per' => '10',  'cache' => 60)
       , array('hits' => 3,  'per' => '30',  'cache' => 120)
       , array('hits' => 10, 'per' => '60',  'cache' => 360)
       , array('hits' => 10, 'per' => '600', 'cache' => 180)   
    );
    
    /** table multipler */
    private $_cache_threshold = 2;
        
    private $_cache_engine = 'file';
    
    
    /** last checked item */
    private $_cache_item;
    
    private $_enabled;
    
    /**
    * Pissdrunk construct
    */
    function __construct() {
        $this->cache  = core::lib('cache')->get_engine($this->_cache_engine);
        $this->handle = core::get_instance()->class_register('page_cache', array('no_preload' => true));
        $this->_enabled = !core::get_instance()->get_cfg_var('disable_page_cache', false);
        
        if (!empty($this->_cache_threshold) && $this->_cache_threshold != 1) {
            foreach ($this->_threshold_table as $k => $v) {
                $this->_threshold_table[$k]['cache'] = $v['cache'] * $this->_cache_threshold;   
            }
        }
    }
    
    
    /**
    * Page hit
    * $cacher->cache_page($_SERVER['REQUEST_URI'], $this->lib('auth')->get_user());
    */
    function cache_page($uri, $buffer) {        
        
        if (!$this->_enabled) return false; 
            
            $hash = $this->_hash($uri);
            core::dprint('cache_page - prepare ' . $uri . ' x ' . $hash);
            
            // if cached item already loaded
            if ($this->_cache_item)
                $item = $this->_cache_item;
            else
                $item = $this->get_by_hash($hash);
            
            if ($item) {       
            
                $just_cached = false;        
                /*
                 array('hits' => 10, 'per' => '600', 'cache' => 180)
                */
                if (!$item->is_cached()) {
                
                    $period = $item->access_time - $item->prev_access_time;
                    
                    foreach ($this->_threshold_table as $row) {  
                        if ($row['per'] <= $period 
                            && ($row['hits'] - 1) <= $item->counter) {
                                // found rule
                                core::dprint('caching page : ' . @intval($row['hits']));
                                $this->_cache_page($item, $buffer, $row);                                
                                $just_cached = true;   
                                break;
                            }                 
                    }
                    
                }
                
                // already hitted
                if (!$just_cached)
                    $item->hit(); 
                    
            }
            else {
                // new
                $this->handle->create(array(
                    'url'       => $uri    
                  , 'url_hash'  => $hash
                ));
            }

    }
    
    /**
    * Method calls from core (after self::get_page_cache) 
    * if cache is present
    * 
    * @param string rfc date
    * @return bool true if not modified
    */
    function last_modified($lm_client) {
        if (!$this->_cache_item) return false;        
        
        $lm_server = $this->_cache_item->prev_access_time;
        $last_modified = date('r', $lm_server);

            //var_dump($lm_client , $lm_server);
//            die;
        
        if (!empty($lm_client)) {
            $lm_client = strtotime($lm_client);        

            if ($lm_client >= $lm_server) {
                if (!headers_sent()) {
                    header('HTTP/1.0 304 Not modified');
                    header('Cache-Control: max-age=86400, must-revalidate');
                }
                return true;
            }
        }
        
        if (!headers_sent()) { 
            header('Last-Modified: ' . $last_modified);
            header('Cache-Control: max-age=86400, must-revalidate');
        }
        return false;
    }
    
    /**
    * Gets cache from storage 
    * @return mixed content or false if cache not found or disabled
    */
    public function get_page_cache($hash, $is_hash = true) {
        if (!$this->_enabled) return false;
        
        $hash = $is_hash ? $hash : $this->_hash($hash);
        $this->_cache_item = $this->get_by_hash($hash);
        
        if ($this->_cache_item && $this->_cache_item->is_cached()) {
            $this->_cache_item->hit();
            
            if ($this->_cache_item->is_expired())
                 $this->_expire_cache($hash);
            else                 
                return $this->cache->get($hash);
            
        }
        
        return false;
    }                 
    
    /**
    * Cache this
    * @param page_cache_item current
    * @param string buffer
    * @param array rules set
    */
    private function _cache_page(page_cache_item $p, $buffer, $rule) {
        $p->cache_hit($rule['cache']);
        $this->cache->set($p->url_hash, $buffer);
    }
    
    /**
    * Removes cache
    */
    private function _expire_cache($hash) {
         $this->cache->remove($hash);  
    }
    
    /**
    * Gets some hash
    */
    private function _hash($what) {
        return functions::hash($what);        
    }

    /**
    * By hash
    */
    function get_by_hash($hash) {
        return $this->handle->clear()
            ->set_where("url_hash = '%s'", $hash)
            ->set_limit(1)
            ->load()
            ->get_item();
    }
    
    /**
    * Is enabled
    */
    function is_enabled() {
       return ($this->_enabled
             && !loader::in_ajax()
             && !loader::in_shell()
             && core::lib('auth')->get_user()->is_anonymous());
    }

    
}