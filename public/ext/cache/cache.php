<?php
/**
 * @author    Vadym Timofeyev <tvad@mail333.com> http://weblancer.net/users/tvv/
 * @copyright 2007 Vadym Timofeyev
 * @license   http://www.gnu.org/licenses/lgpl-3.0.txt
 * 
 * Cacher
 * 
 * engine.cfg
 * [lib_cache]
 * cache_rate = 50
 * memcached = "unix:///tmp/memcached.socket"   
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: cache.php,v 1.3.4.2 2013/12/18 19:38:54 jack Exp $
 */ 
 
 class cache {
     
     /**
     * Cache type: memcache
     * Cache type: file
     */
     private $engines;
     
     /**
     * Rate
     * 0 - disabled
     * 100 - max
     */
     private $rate = 0;
     
     private $_base_dir;
     
     /**
     * Create
     */
     function __construct() {       
       
     }
     
     private $_config = array();
     
     function configure($c) {
         $this->_config = $c;
         // note! fill cache_rate under [lib_cache] section, or cacher will be disabled
         $this->rate = @$c['cache_rate'];
     }
    
    /**
    * @param string root with trail slash!
    */
    function set_root($base, $absolute = false) {
        if (!$absolute)
            $this->_base_dir = $base . /*DIRECTORY_SEPARATOR  .*/ 'opcacher' . DIRECTORY_SEPARATOR;   
        else $this->_base_dir = $base;
        return $this;
    }
    
    function get_root() { return $this->_base_dir; }     
     
     /**
     * Set cache rate
     */
     function set_rate($rate) {
         $this->rate = $rate;
     }
     
     /**
     * Make id for cache handle
     */
     function make_id(string $domain, array $data) {
         return $domain . '_' . md5(serialize($data));
     }
     
     /**
     * Checks cacher is enabled
     */
     function enabled() {
         return ($this->rate > 0);
     }
     
     /**
     * Init for drivers _init_engine(driver) 
     */
     function _init_engine($e) {
         if (isset($this->engines[$e])) return; 
         
         require_once (dirname(__FILE__) . '/drivers/' . $e . '.php');
         
         $method = '_init_' . $e;
         if (is_callable(array($this, $method))) {
             call_user_func(array($this, $method));
         }
         else {
             $this->engines[$e] = false;
         }
     }
     
     function _init_file() {
         if (!isset($this->engines['file'])) {
            $this->engines['file'] = new MultiCacheFile();
            $this->engines['file']->configure($this->_config);
         }
         return $this->engines['file'];
     }
     
     function _init_mem() {           
         if (!isset($this->engines['mem'])) {
         
            $memcache_domain = 'i' . substr(md5(loader::get_root()), 27);
         
             try {
                $this->engines['mem'] = new MultiCacheMemcache();             
                $this->engines['mem']->set_domain($memcache_domain);
                $this->engines['mem']->set_conn_string(@$this->_config['memcached']);
                $this->engines['mem']->getMemcache();
             }
             catch (exception $e) {
                 // no memcache
                 core::dprint('memcached not available: ' . $e->getMessage(), core::E_ERROR);
                 $this->engines['mem'] = false;
             }
             
         }
         
         return $this->engines['mem'];

     }
     
     function _init_apc() {
         if (!isset($this->engines['apc'])) {
             
             $apc_domain = 'i' . substr(md5(loader::get_root()), 27);
             
             try {
                $this->engines['apc'] = new MultiCacheApc();             
                $this->engines['apc']->set_domain($apc_domain);
                $this->engines['apc']->set_conn_string(@$this->_config['apc']);
                $this->engines['apc']->getApc();
             }
             catch (exception $e) {
                 // no apccache
                 core::dprint('apc not available: ' . $e->getMessage(), core::E_ERROR);
                 $this->engines['apc'] = false;
             }       
         }
         return $this->engines['apc'];  
     }
     
     /**
     * Check is memcached loaded
     */
     function has_memory() {
         $this->_init_engine('mem');
         return $this->engines['mem'] ? true : false;
     }
     
     /**
     * Memory handle (memcache)
     * @return MulticacheMemcache
     */
     function get_memory_handle() {
         $this->_init_engine('mem');
         return $this->engines['mem'] ? $this->engines['mem'] : false;
     }
     
     /**
     * Get APC
     * @return MultiCacheAPC
     */
     function get_apc_handle() {
         $this->_init_engine('apc');
         return $this->engines['apc'] ? $this->engines['apc'] : false;
     }

     /**
     * File handle (filecache)
     * @return MultiCacheFile
     */
     function get_file_handle() {
         $this->_init_engine('file');
         return isset($this->engines['file']) ? $this->engines['file'] : false;
     }
     
     
     /**
     * Get cache engine
     * @return MultiCache
     */
     function get_engine($e) {
         if (!isset($this->engines[$e])) $this->_init_engine($e);
         return isset($this->engines[$e]) ? $this->engines[$e] : false;
     }
     
 }


/**
 * Cache driver
 * Class MultiCache
 */
abstract class MultiCache {
    /**
     * Cache max size, bytes.
     * If maxSize == -1 use cache driver specific value for cache max size.
     * @var integer
     */
    public $maxSize = 0;

    /**
     * Cache max items count
     * @var integer
     */
    public $maxItemsCount = 0;

    /**
     * Clean cache frequensy factor.
     * Clean cache operation will start randomically with random factor N if cache overflow.
     * @var integer
     */
    public $cleanCacheFactor = 10;

    /**
     * Class constructor. Setup primary parameters.
     * @param array $params Primary properties
     */
    public function __construct($params = array()) {
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }

      /** @abstract lib configure */
     function configure($c) {
     }
    
    /**
     * Get data
     * @param mixed $key The key that will be associated with the item
     * @param mixed $default Default value
     * @return mixed Stored data
     */
    public abstract function get($key, $default = null);

    /**
     * Store data
     * @param string $key The key that will be associated with the item
     * @param mixed $value The variable to store
     * @param integer $expire Expiration time of the item. Unix timestamp or number of seconds.
     */
    public function set($key, $value, $expire = null) {
        // Check cache limits
        $err = null;
        if (($m = $this->getMaxItemsCount()) > 0 && $this->getItemsCount() >= $m) {
            $err = "Maximum items count attained!";
        }
        if (($m = $this->getMaxSize()) > 0 && $this->getSize() >= $m) {
            $err = "Maximum items count attained!";
        }

        // Check error
        if ($err != null) {
            // Check clean cache factor
            if ($this->cleanCacheFactor > 0 && mt_rand(0, $this->cleanCacheFactor - 1) == 0) {
                $this->clean();

                // Secondary check cache limits
                if ((!($m = $this->getMaxItemsCount()) || $this->getItemsCount() < $m) &&
                    (!($m = $this->getMaxSize()) || $this->getSize() < $m))
                {
                    return;
                }
            }
            throw new Exception($err);
        }
    }

    /**
     * Remove data from the cache
     * @param string $key The key that will be associated with the item
     */
    public abstract function remove($key);

    /**
     * Remove all cached data
     */
    public abstract function removeAll();

    /**
     * Clean expired cached data
     */
    public abstract function clean();

    /**
     * Get items count
     * @return integer Items count
     */
    public abstract function getItemsCount();

    /**
     * Get cached data size
     * @return integer Cache size, bytes
     */
    public abstract function getSize();

    /**
     * Get cache max size. If maxSize == -1 use cache driver specific value of cache max size
     * @return integer Cache maximum size, bytes
     */
    public function getMaxSize() {
        return $this->maxSize >= 0 ? $this->maxSize : $this->getTotalMaxSize();
    }

    /**
     * Get total cache max size.
     * @return integer Cache maximum size, bytes
     */
    public function getTotalMaxSize() {
        return 0;
    }

    /**
     * Get max items count
     * @return integer Maximum items count
     */
    public function getMaxItemsCount() {
        return $this->maxItemsCount;
    }
}



