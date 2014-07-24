<?php

/**
 * Tpl parser loader
 * 
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: tplparser.php,v 1.4.2.1.2.8 2013/11/01 09:41:45 Vova Exp $
 */ 
 
// is the filesystem path where you want the files written to.
define('ASSET_COMPILE_OUTPUT_DIR', loader::get_public('assets/cache'));

// is how that directoy is accessible as an URL relative to the server root.
define('ASSET_COMPILE_URL_ROOT', '/assets/cache');
 
/**
* smarty mock, if no parser
*/
class tpl_parser_mock {
    function assign($var, $val = '') { ; }
    function clear_assign() { ; }
} 
  
/**
* parser class
* loads smarty of another parser
*/
class tpl_loader {     

    static $template_dir; 
    private static $parser;
    
    static function _make_key($p) {
        return sprintf('%u', crc32(serialize($p)));
    }
    
    static function _init_null() {
        core::dprint('[tplparser] using mock');
        return new tpl_parser_mock(); 
    }
    
    /**
    * Load smarty3 
    * @param mixed $params
    * @return Smarty3
    */
    static function _init_smarty3($params) {
        
        $smarty = false;
        
        // check smarty autoload 
        $status = class_exists('Smarty'); 
        
        $file = loader::get_public() . loader::DIR_EXT . 'smarty3/Smarty.php';

        if (!file_exists($file)) {
            throw new lib_exception('Smarty3 file not found');
        }
        
        require $file;
        
        if (!class_exists('Smarty3', false)) {
            throw new lib_exception('Smarty3 class not found');
        }
  
        $smarty = new Smarty3;

        $smarty->debugging          = isset($params['debugging'])       ? $params['debugging'] : core::selfie()->cfg('debug_templates', false);
        $smarty->caching            = isset($params['caching'])         ? $params['caching'] : false;
        $smarty->cache_lifetime     = isset($params['cache_lifetime'])  ? $params['cache_lifetime'] : 120;
        $smarty->cache_locking      = true;
        
        $smarty->compile_check      = isset($params['compile_check'])   ? $params['compile_check'] : true;
        $smarty->force_compile      = isset($params['force_compile'])   ? $params['force_compile'] : false; 
        
        $smarty->merge_compiled_includes = false;
        
        $smarty->error_reporting = error_reporting() &~E_NOTICE;
        
        $smarty->addPluginsDir(loader::get_public() . loader::DIR_EXT . 'smarty3/plugins');

        // custom plugs
        $smarty->addPluginsDir(loader::get_public() . loader::DIR_EXT . 'smarty-plugins');

        // add asset compiler plugin
        $smarty->addPluginsDir(loader::get_public(loader::DIR_EXT . 'smarty-sacy'));
 
        core::dprint(array('[smarty3] dist:%s %s debugging: %s, caching: %s, force: %s, ttl: %d'
            , ($status ? 'composer' : 'old')
            , Smarty3::SMARTY_VERSION
            , ($smarty->debugging       ? 'yes' : 'no')
            , ($smarty->caching         ? 'yes' : 'no')
            , ($smarty->force_compile   ? 'yes' : 'no')
            , $smarty->cache_lifetime)
            , core::E_RENDER);
        
        $template = core::selfie()->cfg('template');
  
        self::$parser = $smarty;
        self::set_template($template);
        
        return $smarty;        
    }
    
    /**
    * Create handle
    * @param array params
    *   engine: null, smarty, smarty3
    */    
    public static function factory($parms, $standalone = false) { 
        
        if (empty($parms)) $params = array();
        if (!isset($parms['engine'])) $parms['engine'] = 'smarty';
        
        $init_action = '_init_' . $parms['engine'];
        
        if (!is_callable(array('tpl_loader', $init_action))) {
            throw new lib_exception('tpl_loader no initializer for : ' . $init_action);
        }
        
        $parser = call_user_func(array('tpl_loader', $init_action), $parms);

        if (!$standalone) self::$parser = $parser;
        
        return $parser;
    }
    
    /**
    * Set root
    */
    static function set_template($template) {

        if (core::in_editor())
          self::$template_dir = loader::get_public() . loader::DIR_EDITOR . loader::DIR_TEMPLATES;
        else 
          self::$template_dir = loader::get_public() . loader::DIR_TEMPLATES . $template;
          
        self::$parser->template_dir   = self::$template_dir;
        
        /*
        * If using template, compile directory must exists /cache/tpl/{template}
        */
        $c_postfix = core::in_editor() ? 'editor/' : $template . '/';
          
        self::$parser->compile_dir    = loader::get_root(loader::DIR_TEMPLATES_C . $c_postfix);
        self::$parser->cache_dir      = loader::get_root(loader::DIR_TEMPLATES_C . $c_postfix) /*. '/cache'*/;
        
        if (!file_exists(self::$parser->compile_dir)) {            
            mkdir(self::$parser->compile_dir, 0777, true); // chmod this right
        }

        /*
        if (!file_exists(self::$parser->cache_dir)) {            
            mkdir(self::$parser->cache_dir, 0777, true); // chmod this right
        }
        */
        
    }

    /**
     * @param bool $new
     * @return Smarty3
     */
    static function get_parser($new = false) {
        return $new ? self::$parser : clone self::$parser;
    }    
}