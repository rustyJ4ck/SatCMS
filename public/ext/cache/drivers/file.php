<?php

/**
 * MultiCacheFile is a class for work with file system storage.
 *
 * @author    Vadym Timofeyev <tvad@mail333.com> http://weblancer.net/users/tvv/
 * @copyright 2007 Vadym Timofeyev
 * @license   http://www.gnu.org/licenses/lgpl-3.0.txt
 * @version   1.01
 * @since     PHP 5.0
 * @example   examples/file/example.php
 */
class MultiCacheFile extends MultiCache {
    /**
     * File cache directory
     * @var string
     */
    public $cacheDir = '.';

    /**
     * Number of cache subderictories
     * @var string
     */
    public $subDirCount = 1;

    /**
     * Length of cache subderictories
     * @var string
     */
    public $subDirLength = 2;

    /**
     * Cache statistics. The structure is: array(count, size)
     * @var array Cache statistics
     */
    private $stats = null;
    
    function __construct() {
        $this->cacheDir = loader::get_root('cache/system');
    }
    
    /**
    * To disable chroot
    * [lib_cache]
    * file_root = cache/system 
    * 
    * @param mixed $c
    */
    function configure($c) {
        if (isset($c['file_root'])) {
            $this->cacheDir = loader::get_root($c['file_root']);
        }
    }

    /**
     * Get data
     * @param mixed $key The key that will be associated with the item
     * @param mixed $default Default value
     * @return mixed Stored data
     */
    public function get($key, $default = null) {
        // Get file name
        $fname = $this->getPathByKey($key);

        // Read file
        if (($data = @file_get_contents($fname)) && ($data = @unserialize($data))) {
            list($value, $expire) = $data;
            if ($expire > 0 && $expire < time()) {
                $this->remove($key);
            } else {
                return $value;
            }
        }
        return $default;
    }

    /**
     * Store data.
     * If expiration time set in seconds it must be not greater then 2592000 (30 days).
     * @param string $key The key that will be associated with the item
     * @param mixed $value The variable to store
     * @param integer $expire Expiration time of the item. Unix timestamp or number of seconds
     */
    public function set($key, $value, $expire = null) {
        parent::set($key, $value, $expire);

        // Get file name
        $fname  = $this->getPathByKey($key, true);
        
        if ($expire > 0 && $expire <= 2592000) {
            $expire = time() + $expire;
        }

        // Create file and save new data
        if (!($fh = fopen($fname, 'wb'))) {
            throw new Exception("File $fname not created!");
        }
        flock($fh, LOCK_EX);
        fwrite($fh, serialize(array($value, $expire)));
        flock($fh, LOCK_UN);
        fclose($fh);
    }

    /**
     * Remove data from the cache
     * @param string $key The key that will be associated with the item
     */
    public function remove($key) {
        // Get file name
        $fname = $this->getPathByKey($key);

        // Delete file
        if (is_file($fname)) {
            if (!unlink($fname)) {
                throw new Exception("File $fname not deleted!");
            }
            if ($this->stats && $this->stats[0] > 0) {
                $this->stats[0]--;
            }
        }
    }

    /**
     * Remove all cached data
     */
    public function removeAll() {
        self::rmdir($this->cacheDir);
        $this->stats = null;
    }

    /**
     * Clean expired cached data
     */
    public function clean() {
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cacheDir)) as $file) {
            $this->get(@base64_decode(basename($file)));
        }
        $this->stats = null;
    }

    /**
     * Get items count
     * @return integer Items count
     */
    public function getItemsCount() {
        if ($this->stats != null) {
            $this->stats = $this->getStats();
        }
        return $this->stats[0];
    }

    /**
     * Get cached data size
     * @return integer Cache size, bytes
     */
    public function getSize() {
        if ($this->stats != null) {
            $this->stats = $this->getStats();
        }
        return $this->stats[1];
    }

    /**
     * Get cache statistics
     * @return array Cache statistics
     */
    public function getStats() {
        $cnt = 0;
        $size = 0;
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cacheDir)) as $file) {
            $cnt++;
            $size += filesize($file);
        }
        return array($cnt, $size);
    }

    /**
     * Remove all files and subdirectories
     * @param string $dir Directory name
     */
    private static function rmdir($dir) {
        $dir = new RecursiveDirectoryIterator($dir);
        foreach(new RecursiveIteratorIterator($dir) as $file) {
            @unlink($file);
        }
        foreach($dir as $subDir) {
            if(!@rmdir($subDir)) {
                self::rmdir($subDir);
                @rmdir($subDir);
            }
        }
    }

    /**
     * Get file path by key
     * @param string $key The key that will be associated with the item
     * @param boolean $ismkdir If true this function creates new subdirectories
     * @return string File path
     */
    protected function getPathByKey($key, $ismkdir = false) {
        $fname = base64_encode($key);
        $dir = $this->cacheDir;

        if ($i = $this->subDirCount) {
            if (strlen($fname) > 250) {
                throw new Exception("Hash for key [$key] is bigger then 250 characters!");
            }
            $len = $this->subDirLength;
            $fcode = $fname;

            while ($i-- > 0) {
                $dcode = substr($fcode, 0, $len);
                if (strlen($dcode) < $len) {
                    break;
                }
                $dir .= "/$dcode";
                if ($ismkdir && !is_dir($dir) && !mkdir($dir, 0777)) {
                    throw new Exception("Directory $dir not created!");
                }
                $fcode = substr($fcode, $len);
            }
        }
        return "$dir/$fname";
    }
}

/**
 * MultiCacheFileString is a class for work with file system storage
 * for keeping simple strings without expiration. The key is simple string too.
 *
 * @author    Vadym Timofeyev <tvad@mail333.com> http://weblancer.net/users/tvv/
 * @copyright 2007 Vadym Timofeyev
 * @license   http://www.gnu.org/licenses/lgpl-3.0.txt
 * @version   1.01
 * @since     PHP 5.0
 * @example   examples/file/example.php
 */
class MultiCacheFileString extends MultiCacheFile {
    /**
     * Get data
     * @param mixed $key The key that will be associated with the item
     * @param mixed $default Default value
     * @return mixed Stored data
     */
    public function get($key, $default = null) {
        $fname = $this->getPathByKey($key);
        return is_file($fname) ? file_get_contents($fname) : $default;
    }

    /**
     * Store data.
     * If expiration time set in seconds it must be not greater then 2592000 (30 days).
     * @param string $key The key that will be associated with the item
     * @param string $value The variable to store
     * @param integer $expire Expiration time of the item. Unix timestamp or number of seconds
     */
    public function set($key, $value, $expire = null) {
        MultiCache::set($key, $value, $expire);
    
        // Get file name
        $fname = $this->getPathByKey($key, true);

        // Create file and save new data
        if (!($fh = fopen($fname, 'wb'))) {
            throw new Exception("File $fname not created!");
        }
        flock($fh, LOCK_EX);
        fwrite($fh, $value);
        flock($fh, LOCK_UN);
        fclose($fh);
    }

    /**
     * Get file path by key
     * @param string $key The key that will be associated with the item
     * @param boolean $ismkdir If true this function creates new subdirectories
     * @return string File path
     */
    protected function getPathByKey($key, $ismkdir = false) {
        $dir = $this->cacheDir;
        if ($i = $this->subDirCount) {
            $fcode = $key;
            $len = $this->subDirLength;
            while ($i-- > 0) {
                $dcode = substr($fcode, 0, $len);
                if (strlen($dcode) < $len) {
                    break;
                }
                $dir .= "/$dcode";
                if ($ismkdir && !is_dir($dir) && !mkdir($dir, 0777)) {
                    throw new Exception("Directory $dir not created!");
                }
                $fcode = substr($fcode, $len);
            }
        }
        return "$dir/$key";
    }
}

/**
 * MultiCacheFileSimple is a class for work with file system storage
 * for keeping simple strings without expiration. The key is simple string too.
 * This cache class doesn't check cache overflow and keeps items in single directory.
 *
 * @author    Vadym Timofeyev <tvad@mail333.com> http://weblancer.net/users/tvv/
 * @copyright 2007 Vadym Timofeyev
 * @license   http://www.gnu.org/licenses/lgpl-3.0.txt
 * @version   1.01
 * @since     PHP 5.0
 * @example   examples/file/example.php
 */
class MultiCacheFileSimple extends MultiCacheFile {
    /**
     * Get data
     * @param mixed $key The key that will be associated with the item
     * @param mixed $default Default value
     * @return mixed Stored data
     */
    public function get($key, $default = null) {
        $fname = $this->cacheDir . '/' . $key;
        return is_file($fname) ? file_get_contents($fname) : $default;
    }

    /**
     * Store data.
     * If expiration time set in seconds it must be not greater then 2592000 (30 days).
     * @param string $key The key that will be associated with the item
     * @param string $value The variable to store
     * @param integer $expire Expiration time of the item. Unix timestamp or number of seconds
     */
    public function set($key, $value, $expire = null) {
        // Get file name
        $fname = $this->cacheDir . '/' . $key;

        // Create file and save new data
        if (!($fh = fopen($fname, 'wb'))) {
            throw new Exception("File $fname not created!");
        }
        flock($fh, LOCK_EX);
        fwrite($fh, $value);
        flock($fh, LOCK_UN);
        fclose($fh);
    }
}
