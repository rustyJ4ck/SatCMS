<?php

/**
 * MultiCacheApc is a class for work with Apc storage.
 *
 * @author    Vadym Timofeyev <tvad@mail333.com> http://weblancer.net/users/tvv/
 * @copyright 2007 Vadym Timofeyev
 * @license   http://www.gnu.org/licenses/lgpl-3.0.txt
 * @version   1.00
 * @since     PHP 5.0
 * @example   examples/Apc/example.php
 */
class MultiCacheApc extends MultiCache {
    
    private $domain = '';
    
    private $conn_string = '';
  
    /**
     * @var array Apc statistics
     */
    private $stats = null;

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
        
        if (!function_exists('apc_store')) {
            throw new Exception('Apc extension not loaded');
        }       
        
        return parent::__construct($params);
    }
    
    function set_conn_string($s) {
        $this->conn_string = $s;
        $this->host = $this->conn_string;
        $this->port = 0;
    }
    
    function getApc() {
        return true;
    }
       

    /**
     * Get data
     * @param mixed $key The key that will be associated with the item
     * @param mixed $default Default value
     * @return mixed Stored data
     */
    public function get($key, $default = null) {
        $value = apc_fetch($this->domain . $key, $result);          
        return $result ? $value : $default;
    }

    /**
     * Store data
     * @param string $key The key that will be associated with the item
     * @param mixed $value The variable to store
     * @param integer $expire Expiration time of the item. Unix timestamp or number of seconds
     */
    public function set($key, $value, $expire = 0) {
        //parent::set($key, $value, $expire);
        apc_store($this->domain . $key, $value, $expire);
    }

    /**
     * Remove data from the cache
     * @param string $key The key that will be associated with the item
     */
    public function remove($key) {
        if (apc_delete($this->domain . $key) && $this->stats && $this->stats['curr_items'] > 0) {
            $this->stats['curr_items']--;
        }
    }

    /**
     * Remove all cached data
     */
    public function removeAll() {
        
        // @todo
        return;
        
        if (!$items = $this->getStats('items')) {
            return;
        }
        $Apc = $this->getApc();
        foreach ($items['items'] as $key => $item) {
            $dump = $Apc->getStats('cachedump', $key, $item['number'] * 2);
            foreach (array_keys($dump) as $ckey) {
                $Apc->delete($ckey);
            }
        }
        $this->stats = null;
    }

    /**
     * Clean expired cached data
     */
    public function clean() {
        
        // @todo
        return;        
        
        if (!$items = $this->getStats('items')) {
            return;
        }
        $Apc = $this->getApc();
        foreach ($items['items'] as $key => $item) {
            $dump = $Apc->getStats('cachedump', $key, $item['number'] * 2);
            foreach (array_keys($dump) as $ckey) {
                $Apc->get($ckey);
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
     * Get Apc statistics
     * @param string $param Statistics paramater
     * @return array Apc statistics
     */
    public function getStats($param = null) {
        if ($this->stats != null) {
            // @todo
            $this->stats = array(); // $this->getApc()->getStats();
        }
        return $param ? $this->stats[$param] : $this->stats;
    }

}
