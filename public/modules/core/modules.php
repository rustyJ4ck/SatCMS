<?php

/**
* @package core
* @version $Id: modules.php,v 1.8.2.1.4.4 2012/09/10 05:59:21 Vova Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/

/**
* Core modules factory
*/
  
class core_modules extends registry {
    
    const MOD_NS = '';
    
    /** as array */
    private $modules;
    
    /** main module tag */
    private $main_module = false;
    
    /** cached current router */
    private $_router;
    
    private $_site_config;

    
    /**
    * Construct 
    */
    public function __construct($modules, $sconfig = null) {
        
        $this->modules = $modules;

        $this->_site_config = $sconfig;
        
        if (!empty($modules)) {
            foreach ($modules as $tag => $module) {
                if (!empty($module['autoload'])) {
                    
                    $this->register($tag, $module);    
                    
                    if (!empty($module['main'])) {
                        $this->main_module = $tag;
                    }
                } else {
                    $this->register_deferred($tag, $module);
                }
            }
        }        
    }

    /** 
    * prefix class with module ns 
    * php5.3< disabled
    */
    static function ns($module, $cl = null) {
        return $module . '_' . $cl;  //intentionally
        $cl = empty($cl) ? '' : ('\\' . $cl);
        return (0 /*$module == 'core'*/ ? '' : (self::MOD_NS . $module )) . $cl;  // /*loader::CLASS_PREFIX*/
    }
    
    static function _nsmodule($module) {
        return self::MOD_NS . $module . '\\' . $module;  // /*loader::CLASS_PREFIX*/
    }
    
    function _get_module_config($id) {
        return @$this->_site_config[$id];
    }


    private $_deferred = array();

    private function register_deferred($module, $params) {
        $this ->_deferred[$module] = $params;
    }

    private function resolve_deferred($module) {

        $result = null;

        if (isset($this->_deferred[$module])) {
            $result = $this->register($module, $this->_deferred[$module]);
            unset($this->_deferred[$module]);
        }

        return $result;
    }
    
    /**
    * Register module
    * @throws modules_exception
    * @return core_module
    */
    public function register($module, $params = null) {
        
        $module_class = isset($params['class']) 
            ? $params['class']
            : $module;
        
        $module_class = (isset($params['prefix'])
            ? $params['prefix']
            : loader::CLASS_PREFIX
            ) . $module_class;

        $module_path_orig = loader::DIR_MODULES . $module . '/';    
            
        $module_path = loader::get_public();
        $module_path .= isset($params['path']) 
            ? $params['path']
            : (loader::DIR_MODULES . $module );
        $module_path .= '/';
        
        
         
        $module_file = $module_path;
        $module_file .= (isset($params['file']) 
            ? $params['file']
            : 'module')
            . loader::DOT_PHP;
        
        core::dprint(array('module::register %s, %s', $module, $module_class), core::E_DEBUG0);
        
        if (!fs::file_exists($module_file)) {
            core::dprint_r(array(
                $module_class, $module_file
            ));
            throw new module_exception('Failed to register module ' . $module . '. File does not exists');
        }
        
        require_once $module_file;
        
        if (!class_exists($module_class, 0)) {
            throw new module_exception('Cant load module ' . $module . ', wrong config?');
        }
        
       // autotag module, if alternative class used
       if (!isset($params['tag']) && !empty($params['class'])) $params['tag'] = $module;

       $module_path =  loader::fix_path(
            loader::get_public() . (!empty($params['chroot']) ? $module_path : $module_path_orig)
       );

        $this->set($module, new $module_class($module_path, $params));
        
        $newb = $this->get($module);        
        
        $newb->init_config($this->_get_module_config($module), abs_config::INIT_APPEND);
        
        return $newb;
    }
    
    /**
    * initialize
    * @param integer level
    */    
    public function init($level) {
        if ($this->is_empty()) return;
        
        $method = "init{$level}";
        
        foreach ($this as $o) {
            
            if (method_exists($o, $method)) {
                // core::dprint(array('%s::%s', $o->get_tag(), $method), core::E_DEBUG0);
                call_user_func(array($o, $method));
            }
        }          
    }
    
    /**
    * Get main
    */
    public function get_main() {

        if (empty($this->main_module)) {
            return false;
        }

        return $this->get($this->main_module);
    }
    
    /**
    * Moduleas as array
    */
    public function get_modules() {
        return $this->modules;
    }
    
    /**
    * Sets router flag to module
    */
    public function set_router($tag) {
        $this->_router = null;
        foreach ($this as $item) {
            $item->set_is_router($tag == $item->get_name());
        }
    }
    
    /**
    * Gets route
    * @return &object
    */
    public function get_router() {
        
        if ($this->_router !== null) return $this->_router;
        
           $this->_router = null;
           
           foreach ($this as $item) {  
            if ($item->is_router()) {
                $this->_router = $item;
                break;
            }
        }      
        
        $this->_router = $this->_router ? $this->_router : $this->get_main();
        
        return $this->_router;
    }
    
    /**
    * @return core_module|false
    */
    function get_by_alias($a) {
         foreach ($this as $item) if ($item->is_alias($a)) return $item;        
         return false;
    }
    
    /**
    * Override registry get method
    * @throws modules_exception
    */
    public function get($id) {   
    
        if ($id == 'core') return core::get_instance();
         
        $return = parent::get($id);

        if (!$return) {

            // try deferred
            $return = $this->resolve_deferred($id);

            if (!$return)
            throw new module_exception('Try to get unloaded module ' . $id);
        }

        return $return;
    }
    
    /**
    * Run event on every module
    */
    public function event($name, $params = null) {
        if ($this->is_empty()) return;
        /** @var $m core_module */
        foreach ($this as $m) {
            $m->trigger($name, $params);
        }
    }
    

}