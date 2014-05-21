<?php

/**
 * MultiCacheMemcache is a class for work with memcache storage.
 *
 * @author    Vadym Timofeyev <tvad@mail333.com> http://weblancer.net/users/tvv/
 * @copyright 2007 Vadym Timofeyev
 * @license   http://www.gnu.org/licenses/lgpl-3.0.txt
 * @version   1.00
 * @since     PHP 5.0
 * @example   examples/memcache/example.php
 */
class MultiCacheMemcache extends MultiCache {
    
    private $domain = '';
    
    private $conn_string = '';
    
    /**
     * Memcache handler
     * @var class Memcache
     */
    private $memcache = null;

    /**
     * @var array Memcache statistics
     */
    private $stats = null;

    /**
     * Memcache host
     * @var string
     */
    public $host = 'localhost';

    /**
     * Memcache port
     * @var integer
     */
    public $port = 11211;

    /**
     * Is memcached connection persistent or no
     * @var boolean
     */
    public $isPersistent = true;
    
    /**
    * Set namespace for vars
    */
    function set_domain($domain) {
        $this->domain = $domain;
    }
    
    /**
    * Create me
    */
    function __construct($params = array()) {
        
        if (!extension_loaded("memcache")) {
            throw new exception('Memcache not available');
        }
        
        return parent::__construct($params);
    }
    
    function set_conn_string($s) {
        $this->conn_string = $s;
        $this->host = $this->conn_string;
        $this->port = 0;
    }

    /**
     * Get Memcache instance
     * @return object Memcache instance
     */
    public function getMemcache() {
        if ($this->memcache === null) {
            
            $this->memcache = new Memcache();
            if ($this->isPersistent && !empty($this->port)) {
                if (!$this->memcache->pconnect($this->host, $this->port)) {
                    throw new Exception("Can't open memcached server persistent connection! " . $this->host . ' :' . $this->port);
                }
            } else {
                if (!$this->memcache->connect($this->host, $this->port)) {
                    throw new Exception("Can't open memcached server connection! " . $this->host . ' :' . $this->port);
                }
            }
        }
        return $this->memcache;
    }

    /**
     * Class destructor. Close opened handlers.
     */
    public function __destruct() {
        if (($memcache = $this->getMemcache()) != null && !$this->isPersistent) {
            $memcache->close();
        }
    }

    /**
     * Get data
     * @param mixed $key The key that will be associated with the item
     * @param mixed $default Default value
     * @return mixed Stored data
     */
    public function get($key, $default = null) {
        $result = $this->getMemcache()->get($this->domain . $key);
        return $result !== false ? $result : $default;
    }

    /**
     * Store data
     * @param string $key The key that will be associated with the item
     * @param mixed $value The variable to store
     * @param integer $expire Expiration time of the item. Unix timestamp or number of seconds
     */
    public function set($key, $value, $expire = 0) {
        parent::set($key, $value, $expire);
        $this->getMemcache()->set($this->domain . $key, $value, false, $expire);
    }

    /**
     * Remove data from the cache
     * @param string $key The key that will be associated with the item
     */
    public function remove($key) {
        if ($this->getMemcache()->delete($this->domain . $key) && $this->stats && $this->stats['curr_items'] > 0) {
            $this->stats['curr_items']--;
        }
    }

    /**
     * Remove all cached data
     */
    public function removeAll() {
        if (!$items = $this->getStats('items')) {
            return;
        }
        $memcache = $this->getMemcache();
        foreach ($items['items'] as $key => $item) {
            $dump = $memcache->getStats('cachedump', $key, $item['number'] * 2);
            foreach (array_keys($dump) as $ckey) {
                $memcache->delete($ckey);
            }
        }
        $this->stats = null;
    }

    /**
     * Clean expired cached data
     */
    public function clean() {
        if (!$items = $this->getStats('items')) {
            return;
        }
        $memcache = $this->getMemcache();
        foreach ($items['items'] as $key => $item) {
            $dump = $memcache->getStats('cachedump', $key, $item['number'] * 2);
            foreach (array_keys($dump) as $ckey) {
                $memcache->get($ckey);
            }
        }
        $this->stats = null;
    }

    /**
     * Get items count
     * @return integer Items count
     */
    public function getItemsCount() {
        return $this->getStats('curr_items');
    }

    /**
     * Get cached data size
     * @return integer Cache size, bytes
     */
    public function getSize() {
        return $this->getStats('bytes');
    }

    /**
     * Get total cache max size.
     * @return integer Cache maximum size, bytes
     */
    public function getTotalMaxSize() {
        return $this->getStats('limit_maxbytes');
    }

    /**
     * Get memcache statistics
     * @param string $param Statistics paramater
     * @return array Memcache statistics
     */
    public function getStats($param = null) {
        if ($this->stats != null) {
            $this->stats = $this->getMemcache()->getStats();
        }
        return $param ? $this->stats[$param] : $this->stats;
    }

    /**
     * Check CURL extension, etc.
     */
    public static function checkEnvironment() {
        if (!extension_loaded('memcache')) {
            throw new Exception('Memcache extension not loaded');
        }
    }
}
