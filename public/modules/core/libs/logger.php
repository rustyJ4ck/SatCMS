<?php

/**
* @package core-libs
* @version $Id: logger.php,v 1.6 2010/07/21 17:57:17 surg30n Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/    
  
/**
* Null logger storage
*/

class logger_storage_null {
    
    private $_file = false;
    
    function __construct($file = false) {
        $this->_file = $file;
    }
    
    function modify($data) {
        echo "<br/><h1>Null logger report:</h1><br/> \n\n" 
           . print_r($data, true);
           
        /*
        if ($this->_file) {
            file_put_contents($this->file)
        }
        */
    }
}
  
/**
* Logger
*/

class tf_logger extends singleton {
    
    /**
    * @private abs_collection or file emu
    */
    private $storage;
    /**
    * @private bool enabled
    */
    private $enabled = true;
    
    /**
    * Creator 
    */
    protected function __construct() {
        
      //  $this->storage = new logger_storage_null();
      //  return;
        
        $this->storage = core::get_instance()->class_register(
            'logs'
            , array('no_preload' => true), true
        );        
        
        // cant create storage, try file-mock!         
        if (!$this->storage)
            $this->storage = new logger_storage_null();
                
    }
    
    /**
    * Toggle logger
    * see disable_logger configuration option
     * @param bool $to
     * @return $this
     */
    function enable($to) {
        $this->enabled = $to;
        return $this;
    }
    
    /**
    * Get storage 
    */
    function get_storage() {
        return $this->storage;
    }

    /**
    * Log stuff
    * @param string title
    * @param string data
    * @param bool is error?
    */
    function log($title, $data = '', $is_error = false) {
        
        if (is_array($title)) $title = vsprintf($title[0], array_slice($title, 1));
        
        if (!$this->enabled) return;
        
        return $this->get_storage()->modify(
            array(
                  'title'   => $title
                , 'date'    => time()
                , 'data'    => $data
                , 'url'     => (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '-')
                , 'domain'  => (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '-')
                , 'error' => $is_error                
            )
        );        
    }
    
    /**
      'file' => string 'C:\web\_site\two-face.ru\htdocs\modules\core\abstract\collection_item.php' (length=73)
      'line' => int 576
      'function' => string '__construct' (length=11)
      'class' => string 'tf_exception' (length=12)
      'type' => string '->' (length=2)
      'args' => 
        array
          0 => &string 'try to get undefined index is_personal data: 1' (length=46)
    */
    
    /**
    * return backtrace
    * Eats memeory like big snake!
    */                              
    public static function debug_backtrace($nl = "\n") {        
        $dbg_trace = debug_backtrace();
        // remove 2 calls from begin
        $dbg_trace = array_splice($dbg_trace, 2, 5);
        foreach ($dbg_trace as $k => $item) {
            if (isset($item['object'])) unset($dbg_trace[$k]['object']);                
            if (!empty($item['args'])) {
                unset($av);
                foreach ($item['args'] as $ak => &$av) {
                    if (is_object($av)) $av = '*' . get_class($av);
                    if (is_array($av))  $av = '*array';
                }
            }
        }
        $dbg_msg = print_r($dbg_trace, 1);
        return $dbg_msg;
    }
    
    /**
    * Error log
    * @param string
    * @param numeric
    * @param string trace from exception
    */
    function error($title, $err_no = 0, $trace = false) {   
        if (is_array($title)) $title = vsprintf($title[0], array_slice($title, 1));          
        $data = empty($trace) ? self::debug_backtrace() : $trace;
        return $this->log($title, $data, true);            
    }
    
    
}