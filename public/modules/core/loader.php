<?php
/**
 *  Loader
 * 
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: loader.php,v 1.15.2.1.2.6 2013/09/29 09:25:09 jack Exp $
 */


 /**
 * Loader
 */
 
 class loader {
     
     /** set _debug > 9 to see loader extra debug messages */
	 public static $_debug = 0;

     /** Recompile core */
     public static $compile = true;

     const DIR_EDITOR       = 'editor/';
     const DIR_MODULES      = 'modules/';
     const DIR_TEMPLATES    = 'templates/';
     const DIR_UPLOADS      = 'uploads/';
     const DIR_EXT          = 'ext/';
     const DIR_LANGS        = 'i18n/';
     
     // htdocs relative
     const DIR_CONFIG       = 'config/';
     const DIR_CACHE        = 'cache/';
     const DIR_TEMPLATES_C  = 'cache/templates/';
     const DIR_TEMP         = 'tmp/';
     
     const DOT_PHP          = '.php';
     const DOT_TPL          = '.tpl';
     const DOT_HTML         = '.html';

     // PHP5.2 class prefix
     const CLASS_PREFIX     = 'tf_';

     /** Framework root */
     protected static $framework_path;

     /** Site root with trailing SLASH/ */
     protected static $root_path;

     /** public with trailing SLASH/ */
     protected static $public_path;

     /** configs path */
     protected static $docs_path;

     /** tmp_path */
     protected static $tmp_path;

     /** upplpath */
     protected static $uploads_path;     
     
     /** indicates ajax calls */
     protected static $_in_ajax = false;
     
     /** in_shell */
     protected static $_in_shell = false;

     protected static $with_composer = false;

     /** @var  \Composer\Autoload\ClassLoader */
     protected static $composer;
     
     /** Checks application in ajax mode */
     public static function in_ajax() {
         return self::$_in_ajax;
     }
     
     /** Checks application in shell mode */
     public static function in_shell() {
         return self::$_in_shell;
     }

     /** Is composer loader used */
     static function with_composer() {
         return self::$with_composer;
     }

     /** @var  array options for loader */
     private static $_options;

     /** test infected */
     const OPTION_TESTING   = 'tdd';

     /** kickstart app */
     const OPTION_AUTORUN   = 'autorun';

     /** framework root */
     const OPTION_ROOT = 'root';

     /** internal debug level */
     const OPTION_DEBUG = 'debug';

     /** core params */
     const OPTION_CORE_PARAMS = 'core_params';

     /** run from cron */
     const OPTION_CRONJOB = 'cronjob';

     /** do not init core */
     const OPTION_NO_INIT = 'no_init';

     public static function _option($option) {
        // return ($options & $option) == $option;
        return @self::$_options[$option];
     }

     /**
      * Entry point
      * @param int options bitmask
      */
     public static function bootstrap($options = array()) {

         self::$_options = $options;

         // debug
         ini_set('display_errors', 'on');
         error_reporting(E_ALL);

         // chained in core::init0
         if (!self::_option(self::OPTION_TESTING) && !loader::$_debug) {
            set_error_handler(create_function('$x, $y', 'if (0 != ini_get("error_reporting")) throw new Exception($y, $x);'), E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING);
         }

         self::$_debug = (int)self::_option(self::OPTION_DEBUG);

         //
         // boot up
         //

         static::_bootstrap();

     }
         
     /**
     * BootStrap kernel
     */
     protected static function _bootstrap() {
         
         // check for php version
         if (intval(phpversion()) < 5) {
             die('Unsupported PHP version.<br/>Require PHP version 5 or greater.<br/>Time to upgrade?');
         }
                  
   		self::$framework_path = self::fix_path(dirname(__FILE__) . '/../../');         
         
         /*
         if (empty($_SERVER['DOCUMENT_ROOT'])) {    
            self::set_root(dirname(__FILE__) . '/../../');   // from shell?
            self::$_in_shell = true;
         }
         else {
            // header('Content-Type: text/html; charset=' . $config['charset']);
            self::set_root($_SERVER['DOCUMENT_ROOT']);   
         }
         */
         
         if (empty($_SERVER['DOCUMENT_ROOT'])) {  
              self::$_in_shell = true;        
         }

         $root = self::_option(self::OPTION_ROOT);

         if (!empty($root)) {
             self::set_root($root);
         }                      
         else {
             // assume TF_ROOT is ./
             self::set_root(dirname(__FILE__) . '/../../../');
         }      
         
         // append include_path, app has more priority to overrides framework files
         set_include_path(get_include_path() . PATH_SEPARATOR . self::$framework_path);

         // ajax check
         if((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            || isset($_REQUEST['with_ajax'])
         ) {
             if ('json' === @$_REQUEST['with_ajax']) {
                 self::$_in_ajax = 'json';
             } else {
                 // 1 - emulated
                 self::$_in_ajax = isset($_REQUEST['with_ajax']) ? 1 : true;
             }
         }

         self::autoload();

         // kick core
         self::core();

         if (self::_option(self::OPTION_NO_INIT)) {
             return;
         }

         /* Functions registered with register_shutdown_function are called before deconstructors, at least as of PHP 5.2.1.
         This contradicts an earlier commenter who claims that objects cannot be utilized in functions called from register_shutdown_function. */
         // register_shutdown_function(array($core, 'shutdown')); 

         // @todo  test env
         if (!self::_option(self::OPTION_TESTING) && class_exists('\Whoops\Run')) {
             self::core()->init();
         }
         else {

             try {

                 self::core()->init();

             } catch (Exception $e) {
                 if (is_callable(array($e, 'display_error')))
                     $e->display_error();
                 else {
                     // No dispaly error in exception
                     if (class_exists('tf_exception', 0))
                         echo tf_exception::generic_display_error($e);
                     else
                         printf("Unknown error : %s\n", $e->getMessage());
                 }
             }
         }


         if (self::_option(self::OPTION_AUTORUN)) {
            self::main();
         }

         core::time_check('core', true, true);

         core::dprint('mount / from ' . self::get_root());
         core::dprint('booting done...');
     }

     private static $_core;

     /**
      * Get kernel
      * @return core
      */
     static function core() {
        return self::$_core ? self::$_core : (self::$_core = core::get_instance(self::_option(self::OPTION_CORE_PARAMS)));
     }

     /**
      * @return core
      */
     static function main() {
         $core = self::core();
         $core->main();
         return $core;
     }
     
     /**
     * Trailing slash
     * realpath on freebsd filter last slash 
     */
     public static function fix_path($path) {
  		 if (!is_dir($path)) return false;
         $path = realpath($path);
         if (DIRECTORY_SEPARATOR !== substr($path, -1, 1)) $path .= DIRECTORY_SEPARATOR;
         return $path;
     }
     
     /**
     * Set root
     * @throws exception
     */
     public static function set_root($root) {

         if (!is_dir($root)) throw new exception('root not found ' . $root);

         self::$root_path    = self::fix_path(realpath($root));

         self::$public_path  = self::$root_path . 'public/';

         self::$docs_path    = self::get_root(self::DIR_CONFIG);
         self::$uploads_path = self::get_public('uploads/');
         self::$tmp_path     = self::get_root(self::DIR_TEMP);

     }
     
     /**
     * Get framework root 
     */
     public static function get_framework_root() {
         return self::$framework_path;
     }       
     
     /**
     * Get doc root dir /  (/)
     */
     public static function get_root($path = '') {
         return self::$root_path . $path;
     }

     /**
      * Get doc root dir /  (public/)
      */
     public static function get_public($path = '') {
         return self::$public_path . $path;
     }
     
     /**
     * Get config dir (tfdocs/)
     */
     public static function get_docs($path = '') {
         return self::$docs_path . $path;
     }
     
     /**
     * Get uploads dir  (uploads/)
     */
     public static function get_uploads($path = '') {
         return self::$uploads_path . $path;
     }
     
     /**
     * Get temp dir  (temp/)
     */
     public static function get_temp($path = '') {
         return self::$tmp_path . $path;
     }
     
     /**
     * Is windows
     */
     public static function is_windows() {
         return (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
     }
     
     static function is_php53() {
         return (version_compare(PHP_VERSION, '5.3.0') >= 0);
     }

     protected static $_preload =  // require loop

         array(

               'exceptions',
               'abstract/common'

             , 'functions'                => 'support/functions'
             , 'strings'                  => 'support/strings'
             , 'fs'                       => 'libs/fs'

             , 'Debug_HackerConsole_Main' => 'console/console'

             , 'abs_data'                 => 'abstract/data'
             , 'abs_config'               => 'abstract/config'
             , 'registry'                 => 'abstract/registry'
             , 'collection_iterator'      => 'abstract/collection/iterator'
             , 'abs_collection'           => 'abstract/collection/collection'

             , 'abs_control'              => 'abstract/controls/abs_control'
             , 'control_number'           => 'abstract/controls/number'
             , 'control_text'             => 'abstract/controls/text'
             , 'control_unixtime'         => 'abstract/controls/unixtime'
             , 'control_boolean'          => 'abstract/controls/boolean'
             , 'control_image'            => 'abstract/controls/image'

             , 'abs_collection_item'      => 'abstract/collection/item'
             , 'collection_filter'        => 'abstract/collection/filter'
             , 'module_orm'               => 'abstract/module_orm'

             , 'module_ioc'               => 'abstract/module_ioc'
             , 'module_blocks'            => 'abstract/module_blocks'
             , 'module_router'            => 'abstract/module_router'
             , 'editor_controller'        => 'abstract/editor_controller'
             , 'module_controller'        => 'abstract/module_controller'

             , 'core_module'              => 'abstract/module'
             , 'output_filter'            => 'abstract/output_filter'

             , 'core_libs'                => 'libs'
             , 'core_modules'             => 'modules'
             , 'core'                     => 'core'

                 // core-libs
             , 'tf_auth'                  => 'libs/auth'

             , 'tf_manager'               => 'libs/manager'
             , 'tf_logger'                => 'libs/logger'
             , 'tf_renderer'              => 'libs/renderer'
             , 'db_loader'                => 'libs/db_loader'
             , 'tpl_loader'               => 'libs/tpl_loader'
             , 'tf_request'               => 'libs/request'

         );

     /**
      * Register core classes with autoloader
      * @param \Composer\Autoload\ClassLoader $loader
      */
     static function preload_core_with_composer($loader) {

         $timer = microtime(1);
         $classes = self::$_preload;

         foreach ($classes as $classname => &$file) {

             $file = self::get_public(self::DIR_MODULES . 'core/' .  $file . self::DOT_PHP);

             if (!is_string($classname)) {
                 require $file;
                 unset($classes[$classname]);
             }
         }

         $loader->addClassMap($classes);

         self::dprint("autoload_core - done %.5f", array(microtime(1) - $timer));
     }

     /**
      * Preload/compile core
      * @param bool $compile
      */
     static function preload_core($compile = false) {

         $timer = microtime(1);

         $compiled_core = self::get_root(self::DIR_CACHE) . 'compiled.core.php';
         $compiled_exists = file_exists($compiled_core);

         if ($compiled_exists) {
             self::dprint("\t..%s", array('Using compiled core'));
             require $compiled_core;
         }
         elseif (!$compiled_exists || $compile) {

            // compile

            $obfuscator = null;
            $compiled_buffer = '';

            if ($compile) {
                require "modules/core/libs/obfuscator.php";
                $obfuscator = new tf_obfucator();
            }

            foreach (self::$_preload as $file) {

                $file = self::get_public(self::DIR_MODULES . 'core/' .  $file . self::DOT_PHP);

                self::dprint("\t..%s", array($file));

                if (!file_exists($file)) {
                    $exception = class_exists('tf_exception', 0) ? 'tf_exception' : 'Exception';
                    throw new $exception ("Loader cannot load - " . $file);
                }

                require $file;

                if ($compile) {
                    $compiled_buffer .= "<?php ### {$file} ?>";
                    $compiled_buffer .= $obfuscator->run($file);
                    if (!preg_match('@\?>[\s]*$@s', $compiled_buffer)) {
                        $compiled_buffer .= '?>';
                    }
                } else {

                }
            }

             // preg_replace(array('#<\?php#','#\? ^space_here_or_error^ >#', '#(\s\#\s|\s\/\/\s).*#', '#/\*.*\*/#mUs' /*, '#[[:space:]]{2,}#' , "#[\n\r]+#"*/), '',

            if ($compile) {
                 $compiled_buffer =
                     "<?php /*Compiled: "
                     . @date('d.m.Y H:i:s')
                     . "*/ ?>"
                     . $compiled_buffer
                     ;

                 file_put_contents($compiled_core, $compiled_buffer);
                 self::dprint("compiled size: %.2f KB", array(strlen($compiled_buffer)/1024));
            }

         }

         // compiled: 0.043 vs 0.0170

         self::dprint("done %.5f", array(microtime(1) - $timer));

     }

     /**
     * Load basic classes files
     * and nothing more
     */
     public static function autoload() {
         
         if (empty(self::$root_path) || !is_dir(self::$root_path)) {
             throw new exception('Bad root! Use loader::set_root()');
         }
         
         // composer autoload
         $autoloader_path = self::get_root('vendor/autoload.php');
         
         if (file_exists($autoloader_path)) {
             self::$composer = require $autoloader_path;
             self::$with_composer = true;
         }                                     

         /** @todo cache compiled core to apc/memcached */

         if (self::$with_composer) {
            self::preload_core_with_composer(self::$composer); // 0.2210 -- 0.2320
         } else {
            self::preload_core(self::$compile); // 0.2090 -- 0.2110
         }
     }
     
     protected static function dprint($f, $v = null) {
         if (self::$_debug < 9) return;
         if (is_array($v)) $f = vsprintf($f, $v);
         echo "[LOADER] " . $f . "\n" . (self::in_shell() ? '' : "<br/>");         
     }
     
 }
   
 
 

 
