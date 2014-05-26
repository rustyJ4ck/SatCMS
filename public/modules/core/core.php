<?php

/**
 * Core
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: core.php,v 1.15.2.7.2.19 2013/12/19 09:15:34 Vova Exp $
 */

/**
 * Core|God object
 */
class core extends core_module /*module_orm*/ {

    /**
     * constructor options:
     * skip_database
     */

    /**
     * Page cache usage flag
     */
    private $_from_cache = false;

    /**
     * Last modified
     */
    private $_last_modified = false;

    /**
     * Core objects
     * linked via @see core::lib()
     *
     * Libs are
     * --------
     * private static $tpl_parser;
     * private static $db;
     * private static $renderer;
     * private static $console;
     * private static $logger;
     * private static $editor;
     */
    private static $libs;

    /**
     * System libs
     * This libs autoregister itself when call it for the first time
     * @see register_system_lib
     * @see load_system_lib
     *
     * loaded from system_libs.cfg
     */
    private static $system_libs = array();

    /**
     * Modules registry
     * loaded in @see core::init0()
     * Get by name via @see core::module()
     * @var core_modules
     */
    private static $modules;

    /** Языковые переменные */
    private $langwords = array();

    /** autoload in init0 */
    protected $preload_classes = array();

    /**
     * Dyn config collection
     */
    protected $dyn_config;

    /**
     * Global Message
     * Sets by @see set_message
     * Gets by @see get_message
     */
    private $message;
    private $message_data;

    /**
     * Язык определяется в cfg_var['lang']
     */

    /**
     * Ouput Filters
     * Current (rss)
     */
    private $output_filter = false;

    const NAME = 'TFEngine';

    /** core version */
    public static $version = "004";

    /** debug level */
    private static $_debug_level = 0;

    /** @var core */
    private static $_instance;

    /** @var ctype_collection */
    private $_ctypes;

    /**
     * Core instance
     *
     * @param mixed  set to `true` for force ::init()
     *
     * @return core
     */
    public static function get_instance($params = array()) {

        if (!isset(self::$_instance)) {
            self::$_instance = new self($params);
        }

        if ($params === true) {
            if (self::$_instance->initialized === self::IS_LOADING) {
                throw new core_exception('Core is loading when ' . __METHOD__);
            }

            if (!self::$_instance->initialized) {
                self::$_instance->init(); // init0
            }
        }

        return self::$_instance;
    }

    /**
     * Get prepared instance
     * @return core
     */
    static function selfie() {
        return self::$_instance;
    }

    /**
     * constructor
     * $params goes to ->cfg('options.$')
     * @throws exception
     */
    public function __construct($params = array()) {

        self::time_check('core-boot');

        // bogus: fix loop with get_instance
        if (!self::$_instance) self::$_instance = $this;

        $initilize_after_load = ($params === true);

        if (empty($params) && !is_array($params)) $params = array();

        $cfg_file = loader::get_docs() . 'engine.cfg';

        if (fs::file_exists($cfg_file)) {
            // echo('[error] Configuration file not found'); 
            $this->init_config(parse_ini_file($cfg_file, true));
        }

        // multiconfig config/domain.engine.cfg

        $host = @$_SERVER['HTTP_HOST'];
        $host = strpos($host, 'www.') === 0 ? substr($host, 4) : $host;

        if ($this->cfg('multidomain_config', false) && $host) {

            $host = str_replace(':', '.', $host); // localhost:8002

            $host_config = loader::get_docs() . $host . '.engine.cfg';

            if (fs::file_exists($host_config)) {
                $this->init_config(parse_ini_file($host_config, true), abs_config::INIT_APPEND);
            }
        }



        setlocale(LC_ALL, ($locale = $this->cfg('locale', 'ru_RU.UTF8')));

        if (loader::is_windows()) {
            list($lang, $codeset) = explode('.', $locale);
            $lang = substr($lang, 0, 2);
            putenv('LANG=' . $lang . '.' . $codeset);
            putenv('LANGUAGE=' . $lang . '.' . $codeset);
            //bind_textdomain_codeset('mydomain', $codeset);
        }

        if (fs::file_exists($libs_file = (loader::get_docs() . 'libs.cfg'))) {
            self::$system_libs = parse_ini_file($libs_file, true);
        }

        self::$libs = new core_libs();

        $duagent = $this->cfg('debugger_agent', 'iamdebugger');

        // compare only lside of agent, because firephp or something add its stuff to end
        if (isset($_SERVER['HTTP_USER_AGENT']) && substr($_SERVER['HTTP_USER_AGENT'], 0, strlen($duagent)) === $duagent
            || !empty($params['debug'])
        ) {

            self::set_debug(
                !empty($params['debug'])
                    ? $params['debug']
                    : $this->cfg('debug', self::E_INFO)
            );

            if (!self::is_debug()) {
                self::register_lib('console', new SatCMS\Modules\Core\Console\FakeConsole);
            }
            else
            if (array_key_exists('console', self::$system_libs)) {
                // load external console
                self::lib('console');

            } else {
                // bind console (modules/core/console)
                self::register_lib('console',
                    new Debug_HackerConsole_Main(
                           !$this->cfg('no_console')
                        && !loader::in_shell()
                        && !loader::in_ajax()
                        || (loader::in_ajax() && $this->cfg('debug_ajax', false))
                    )
                );

            }

        } else {

            if (!loader::in_shell()) {
                self::$_debug_level = false;
                ini_set('display_errors', 'off');
            } else {
                // enable debug messages in shell
                self::set_debug(
                    $this->cfg('shell_debug_level', self::E_INFO)
                );
            }

        }

        if (self::is_debug()
            && (!loader::in_ajax() || $this->cfg('debug_ajax', false))
            && !loader::_option(loader::OPTION_TESTING)
            && class_exists('\Whoops\Run')) {
                $whoops = new \Whoops\Run;
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
                $whoops->register();
                $this->set_cfg_var('with_debugger', true);
        }

        // build module
        parent::__construct(loader::get_public(loader::DIR_MODULES) . __CLASS__ . '/', array('options' => $params));

        if ($initilize_after_load) {
            $this->init();
        }
    }

    /**
     * Check whenever user in editor mode
     */
    public static function in_editor() {
        return self::get_instance()->cfg('options.editor');
    }

    /**
     * set/get isFrontpage
     */
    function in_index($flag = null) {
        if (isset($flag)) {
            $this->set_cfg_var('in_index', $flag);
        }
        return $this->cfg('in_index');
    }

    /**
     * Get identification variable
     * use core::TAG_ constants for naming
     * @return mixed registry or single param
     */
    public static function get_params($name = false) {
        $params = self::lib('request')->get_ident();

        return ($name) ? $params->get($name) : $params;
    }

    /**
     * Get module
     * @return core_module handle
     * @throws core_exception
     */
    public static function module($name) {

        if ($name == 'core') return core::get_instance();

        if (!self::$modules) throw new core_exception('Modules not initialized. Try getting ' . $name);

        return self::$modules->get($name);
    }

    /**
     * Register misc module in the system
     * To register system module @see self::get_modules()->register('name')
     */
    public static function register_module($id, $lib) {
        self::$modules->set($id, $lib);
    }

    /**
     * Register system module in the system
     */
    public static function register_system_module($id) {
        self::$modules->register($id);
    }

    /**
     * All modules in the bulk
     */
    public static function modules() {
        return self::$modules;
    }

    /**
     * All libs in the bulk
     */
    public static function get_libs() {
        return self::$libs;
    }

    /**
     * Add entry for system lib
     * To load lib, use @see self::load_system_lib
     * @param string index
     * @param string fs path
     * @param bool create and return lib
     */
    public static function register_system_lib($name, $file, $load = false) {
        if (!array_key_exists($name, self::$system_libs)) self::$system_libs[$name] = $file;
        if ($load) return self::lib($name);
    }

    /**
     * Load system lib
     * Used @see core::$system_libs
     */
    private static function load_system_lib($name) {

        if (!array_key_exists($name, self::$system_libs)
            || self::$libs->is_registered($name)
        ) return false;

        $filename = '';
        $config   = self::$system_libs[$name];
        $cl_name  = $name;
        $params   = null;

        if (is_array(self::$system_libs[$name])) {

            if (isset($config['file'])) {
                $filename = $config['file'];
            }
            if (isset($config['class'])) {
                $cl_name = $config['class'];
            }

            $params = @$config['params'];


        } else {
            $filename = $config;
        }

        // autoload
        if (!class_exists($cl_name)) {

            if (!$filename) {
                throw new core_exception('Error load system lib ' . $name . ' / ' . $cl_name);
            }

            try {
                fs::req(fs::get_full_path($filename . loader::DOT_PHP), true);
            } catch (fs_exception $fs_e) {
                throw new core_exception('Error load system lib ' . $name . '. ' . $fs_e->getMessage());
            }
        }

        // try with prefix
        if (!class_exists($cl_name, 0)) {
            $cl_name = loader::CLASS_PREFIX . $cl_name;
        }

        if (!class_exists($cl_name, 0)) {
            throw new core_exception('Class doesnt exists ' . $cl_name);
        }

        $plib = new $cl_name ($params);

        return self::register_lib($name, $plib);
    }

    /**
     * Is lib enabled (but not loadede for example)
     */
    public static function lib_enabled($name) {
        return (array_key_exists($name, self::$system_libs)
            || self::$libs->is_registered($name)
        );
    }

    /**
     * Get core lib wrapper
     * Core must be constructed for use this method and core::$libs must be initialized
     *
     * @return object library handle
     */
    public static function lib($name = null) {

        if (!isset($name)) return self::$libs;

        if (!self::$libs) throw new core_exception('Libs not set, query : ' . $name);

        if (!self::$libs->is_registered($name)) self::load_system_lib($name);

        $return = (self::$libs->is_registered($name)) ? self::$libs->get($name) : false;

        // exception when not loaded (skip console)
        if (!$return && $name != 'console') {
            debug_print_backtrace();
            throw new core_exception('Try to request unloaded lib "' . $name . '"', tf_exception::CRITICAL);
        }

        return $return;
    }

    /**
     * Register library
     * configure via lib_{name}
     * @return mixed lib|null if deffered
     */
    public static function register_lib($id, $lib, $config = null) {

        return
            self::get_libs()
                ->configure($id, $config
                        ? $config
                        : self::get_instance()->cfg('lib_' . $id, array())
                )
                ->set($id, $lib)
                ->is_resolved($id)
                ? $lib
                : null //deferred
            ;
    }

    /**
     * Init 10
     */
    function init10() {
        if ($auth = core::lib('auth')) $auth->on_session_end();
    }

    private $_halted = false;

    /**
     * Halt, called @see register_shutdown_function
     * produce init(10)
     */
    function halt() {
        if (!$this->_halted) {
            $this->_halted = true;
            $this->init(10);
        }
        exit();
    }

    /**
     * Call instant ajax answer (raw result)
     * @param mixed data
     */
    function ajax_answer($a, $raw = false) {
        if (!$raw) $this->renderer->content_type_header(tf_renderer::CONTENT_TYPE_JSON);
        echo $raw ? $a : json_encode($a);
        $this->halt();
    }

    /**
     * Function called on most end of script execution
     * and flush all output to user,
     * close connections and make cleanups
     *
     * Shutdown
     *
     * calls renderer::output
     */
    public function shutdown() {

        if (!$this->_from_cache) {

            // send headers if any
            $this->check_last_modified();

            // shutdown
            $this->init(9);

            // in critical errors we have no valid renderer


            if (/** @var tf_renderer $r */ $r = self::lib('renderer')) {

                if (loader::in_ajax()) {

                    $r->output_ajax();

                } else {

                    $cacher = $this->lib_enabled('page_cacher') ? $this->lib('page_cacher') : false;

                    // Cache if no exceptions and cacher ready
                    if ($cacher && !tf_exception::get_last_exception()
                        && $cacher->is_enabled()) {

                        ob_start();
                        $r->output();
                        $buffer = ob_get_contents();
                        ob_end_clean();
                        echo $buffer;
                        // Alright? Cache it
                        $_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        $cacher->cache_page($_url /*, $this->lib('auth')->get_user()*/, $buffer);

                    } else {

                        $r->output();

                    }
                }
            }
        } // cache

        // shutdown_after
        $this->init(91);

        if ($db = self::lib('db')) {
            $db->sql_close();
        }

        $time = self::time_check('core-boot', true);

        if (self::is_debug()) {
            self::cprint('core shutdown : ' . ($this->_from_cache ? 'CACHE : ' : '') . $time . ' ms, mem : ' . memory_get_usage());
        }

        if (!loader::in_ajax()) {
            echo "\n\n<!--\n\tPowered by : " . self::NAME . "\n\tTime elapsed : " . $time . "\n-->\n";
        }

        $this->halt();
    }

    /**
     * INIT0 - call right after create an instance of core
     * create basic stuff
     * @throws core_exception
     */
    public function init0() {

        if ($this->initialized) {
            throw new core_exception('Already initialized');
        }

        $this->initialized = self::IS_LOADING;

        self::dprint(array("core::init0 %s", (loader::with_composer() ? '+composer' : '')), self::E_DEBUG2);

        // templates setup
        self::register_lib('tpl_parser', function() {
            return tpl_loader::factory(
                core::selfie()->cfg('lib_tpl_parser')
                );
        });



        // renderer
        self::register_lib('renderer', function() {
            return
            0 /*loader::in_shell()*/ //disable renderer in console
                ? new \SatCMS\Modules\Core\Base\ObjectMock()
                : new tf_renderer(
                    core::selfie()->cfg('template'), core::lib('tpl_parser')
                );
        });

        // database setup (database-`mysql`)
        $db_cfg_key = $this->cfg('database');

        if ($db_cfg_key) {
            if (is_array($db_cfg_key)) {
                $db_cfg = $db_cfg_key;
            } else {
                $db_cfg_key = 'database-' . $db_cfg_key;
                $db_cfg = $this->cfg($db_cfg_key);
            }
        }

        if (!$db_cfg || $this->cfg('options.skip_database')) {
            $db_cfg['engine'] = 'null';
            self::dprint('Missing database configuration section', core::E_CRIT);
        }

        // default connection
        self::register_lib('db', $db_test = db_loader::get(null, $db_cfg));

        if (!$db_test /*&& defined('TF_TEST_INFECTED')*/) {
            throw new core_exception('Database connection problem', tf_exception::CRITICAL);
        }

        // set default timezone
        $tz = $this->cfg('default_timezone');
        date_default_timezone_set($tz ? $tz : 'Europe/Moscow');

        // load core config
        $this->dyn_config = $this->model('config', array('render_by_key' => true))->load()->merge_with($this->config);

        // content-types
        $ctype_config = loader::get_docs() . 'ctypes.cfg';
        $ctype_array  = (fs::file_exists($ctype_config))
            ? parse_ini_file($ctype_config, true)
            : array();

        $this->_ctypes = $this->get_ctype_handle();
        $this->_ctypes->from_array($ctype_array);

        // add libs
        self::register_lib('logger', function() {
            return tf_logger::get_instance()->enable(!core::get_instance()->cfg('disable_logs', false));
        });

        self::register_lib('manager', new tf_manager());
        self::register_lib('request', new tf_request());

        $modules_config = array();

        if ('file' == $this->cfg('modules_config', '')
            && ($modules_config_file = loader::get_docs() . 'modules.cfg')
            && fs::file_exists($modules_config_file)
        ) {
            $modules_config = parse_ini_file($modules_config_file, true);
        } else {
            try {
                $modules_config = $this->module('modules', array('key' => 'tag'))->as_array();
            } catch (module_exception $e) {
                // misconfigured modules, some of modules not exists
                throw new core_exception($e->getMessage(), tf_exception::CRITICAL);
            }
        }

        // site init %domain%
        // config/%domain%/init.php
        $site_config      = array();
        $site_config_path = $this->cfg('site_config');
        if (!empty($site_config_path)) {
            $host = @$_SERVER['HTTP_HOST'];
            if ('%domain%' == $site_config_path) {
                $site_config_path = ((strpos($host, 'www.') === 0) ? substr($host, 4) : $host);
            }

            $mod_config_file = loader::get_docs() . $site_config_path . '/init.php';

            if ($site_config_path && file_exists($mod_config_file)) {
                $site_config = include($mod_config_file);
            }
        }

        // import module config `mod_{module}`
        // allow overrides modules.cfg
        foreach ($this->config as $cfg_key => $cfg) {
            if (strpos($cfg_key, 'mod_') === 0) {
                $cfg_key = substr($cfg_key, 4);
                $modules_config[$cfg_key] = @$modules_config[$cfg_key] ?: array();
                $modules_config[$cfg_key] = functions::array_merge_recursive_distinct($modules_config[$cfg_key], $cfg);
            }
        }

        // module manager
        self::$modules = new core_modules($modules_config, $site_config);

        // finish core init0 proccess
        parent::init0();

        // check bans
        if (!$this->cfg('no_bans_check')
            && isset($_SERVER['REQUEST_URI'])
            && ($_uri = $_SERVER['REQUEST_URI']) && !empty($_uri)
        ) {
            if ($this->get_bans_handle()->check_spam($_uri))
                throw new core_exception(i18n::T('you_are_banned'), tf_exception::CRITICAL);
        }

        // register auth
        if ($musers = $this->module('users'))
            self::register_lib('auth', new tf_auth(
                $musers,
                loader::in_shell()
            ));

        if (self::in_editor()) {
            // editor kickstart
            $this->lib('editor');
            //self::register_lib('editor', new tf_editor());
        }

        register_shutdown_function(array($this, 'halt'));

        $this->initialized = true;
    }

    // -----------------------------------------

    /**
     * Void Main
     */
    function main() {
        $this->run();
        $this->shutdown();
    }

    // -----------------------------------------

    /**
     * Run module
     */
    function run() {

        // Watch for cache 
        if (($cacher = $this->lib('page_cacher')) && $cacher->is_enabled()) {

            $_url   = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $cached = $cacher->get_page_cache($_url, false);

            if ($cached) {

                $cl_last_mod  = ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
                $not_modified = $cacher->last_modified($cl_last_mod);

                $this->_from_cache = true;

                if (!$not_modified) {
                    echo $cached;
                }

                return;
            }
        }
        // Output cached content and return if any

        // route request

        try {

            $this->dispatch();

        } catch (controller_exception $e) {

            $this->set_message('controller_exception', array($e->getMessage()));

        } catch (router_exception $e) {

            switch ($e->getCode()) {
                // crit.hit
                case router_exception::CRITICAL:
                    self::dprint('[ROUTER] critical hit ' . $e->getMessage());
                    $this->shutdown_critical($e->getMessage());
                    break;

                // 404
                case router_exception::NOT_FOUND:
                    self::dprint('[ROUTER] not found : ' . $e->getMessage());
                    $this->error404($e->getMessage());
                    break;

                // undefined
                default:
                    self::dprint('[ROUTER] unhandled route error x' . $e->getCode() . ' - ' . $e->getMessage());
                    // editor bad login trigger router_exception?
                    $this->error404($e->getMessage());
                    break;
            }

            self::dprint('[ERROR] Shutdown ' . __METHOD__);

            // down
            $this->shutdown();

        }

        /*
        catch (controller_exception $e) {
            self::dprint('[CONTROLLER] no template?');
            $this->shutdown_critical($e->getMessage());
        }
        */

        /*

        // Whoops?
        // Catch for non-debug clients

        catch (Exception $e) {

            $this->renderer->disable_output(1);

            if (is_callable(array($e, 'display_error'))) {
                $e->display_error();
            }
            else {
                // No dispaly error in exception
                if (class_exists('tf_exception', 0))
                    echo tf_exception::generic_display_error($e);
                else
                    printf("Unknown error : %s\n", $e->getMessage());
            }

        }

        */


    }

    /**
     * Get potential cname (external domain)
     * Faculties supports aliases, this it!
     */
    function get_cname() {
        return $this->cfg('cname');
    }

    /**
     * Entry point for app
     *
     * @return bool false - not found
     * @throws router_exception
     */
    function dispatch($url = false) {

        if ($this->cfg('options.log_requests')) {
            $this->logger->log('request', $url);
        }

        $request = $url ? $url : urldecode($_SERVER['REQUEST_URI']);

        self::modules()->event('route_before', array('url' => &$request));

        $skip_site_check = false;

        if (preg_match('@users/(login|logout)/\z@', $request)) {
            $this->set_cfg_var('in_login', true);
            $skip_site_check = true;
        }

        // returns HTTP_HOST if main_domain not set
        $domain = $_SERVER['HTTP_HOST']; //$this->get_main_domain();
        $subdomain   = ($_SERVER['HTTP_HOST'] != $domain) ? substr($_SERVER['HTTP_HOST'], 0, (-1 + -1 * strlen($domain))) : false;

        if ('www' === $subdomain) $subdomain = false;
        $this->set_cfg_var('subdomain', $subdomain);

        $cname = false;

        // check for cname
        if ($domain != substr($_SERVER['HTTP_HOST'], (-1 * ($sl = strlen($domain))), $sl)) {
            $cname = preg_replace('/^.*\.([^\.]+\.[\w]+)$/', '$1', $_SERVER['HTTP_HOST']);
            $this->set_cfg_var('cname', $cname);
        }

        // @todo fix tails or errors
        if (false !== ($qpos = strpos($request, '?'))) {
            $request = substr($request, 0, $qpos);
        }

        $dispatcher = self::$modules->get_main();

        if (!$dispatcher) {
            throw new module_exception('Main module not set');
        }

        $this->append_base_url($this->router->get_protocol());
        $this->append_base_url($this->cfg('main_domain'), true);

        if ($dispatcher && functions::is_callable(array($dispatcher, 'predispatch'))) {
            $dispatcher->predispatch($domain, $request);
        }

        /**
         * /domain/module+alias/
         * check path against
         *   - modules list
         *   - alias list
         */

        $request_array = explode('/', $request);

        // remove empty parts
        array_splice($request_array, 0, 1);
        if (empty($request_array[count($request_array) - 1]))
            array_splice($request_array, -1, 1);

        $request_count = count($request_array);

        // Output filters
        if ($request_count >= 2 && $request_array[$request_count - 2] == 'outfilter') {
            $filter = $request_array[$request_count - 1];
            if (!$this->set_output_filter($filter)) {
                throw new router_exception('Invalid filter - ' . $filter, router_exception::NOT_FOUND);
            }
            array_splice($request_array, -2);
            // @todo slice $request
        }

        $router_module = null;

        if ($this->in_index()) {
            // override frontpage layout
            if ($template = $this->cfg('site.frontpage.template')) {
                $this->renderer->set_page_template($template);
            }
        }

        $root = isset($request_array[0]) ? array_shift($request_array) : false;

        if (!empty($root) && !preg_match('/^[\w_\.\-\%а-я[:space:]]+$/ui', $root)) {
            throw new router_exception('Root route error');
        }

        if (empty($root)) {
            // use default module as router
            self::$modules->get_main()->set_is_router();
        } else {

            // core module calls
            if ('core' == $root) {
                $this->set_is_router(true);
                $router_module = $this;
            } else
                if (self::$modules->is_registered($root)) {
                    self::$modules->set_router($root);
                } else {
                    /** @var core_module */
                    $pmod = self::$modules->get_by_alias($root);
                    if ($pmod) $pmod->set_is_router(true);
                    else {
                        // default, inject root element
                        array_unshift($request_array, $root);
                        self::$modules->get_main()->set_is_router();
                        // no module found, route to default router (with main flag)
                    }
                }
        }

        // nothing routed?
        $router_module = $router_module ? $router_module : self::$modules->get_router();

        if (!$router_module) throw new router_exception('No router module found');

        $router = $router_module->get_router();

        if ($router) {
            self::dprint('[ROUTER] using ' . $router_module->get_name());

            $result = $router->route(
                $request_array
            );

            if (!$result) {
                throw new router_exception('Requested page doesnt exists');
            }

            /*deprecated*/
            /*           
            self::lib('renderer')->set_main_template(
                $router_module->get_controller()->get_template()
            );
            */
        } else {
            throw new router_exception('No router available for ' . ($router_module ? $router_module->get_name() : ''), 0);
        }

    }

    /**
     * Set output filter, like rss, etc...
     */
    function set_output_filter($filter) {

        $result = include('modules/core/output_filters/' . $filter . loader::DOT_PHP);

        if (!$result) return false;

        // currently only rss filter supported
        $cl                  = "{$filter}_output_filter";
        $this->output_filter = new $cl ($this);

        core::dprint('Activate filter ' . $filter);

        return true;
    }

    /**
     * Get output filter
     */
    function get_output_filter() {
        return $this->output_filter;
    }

    /**
     * Завершающий init
     */
    public function init9() {}

    /**
     * initialize BULK
     * this function call initXX() where XX = level
     *
     * @param integer level
     *
     * 0  - core-construct-after
     * 9  - shutdown-before
     * 91 - shutdown-after
     * 10 - halt-before @see self::halt()
     *
     * @return void
     */

    public function init($level = 0) {
        $method = "init{$level}";
        if (method_exists($this, $method))
            call_user_func(array($this, $method));

        // looking for modules
        self::$modules->init($level);
    }


    /**
     * Get subdomain
     * @see self::dispatch
     */
    function get_sub_domain() {
        return $this->cfg('subdomain');
    }

    /**
     * Действие, обработчик событий
     * админ панели
     * Повторяет действие для модуля
     * @param string on_{метод}
     * @param array параметры
     */
    public function on_editor_action($action, $parms = array()) {

        $action = "on_editor_{$action}";

        if (method_exists($this, $action)) {
            call_user_func(array($this, $action), $parms);
        }

    }


    /**
     * This method called from cron.php
     * @param string module to run
     */
    function crontab($module = false) {

        if (empty($module)) {
            $this->lib('logger')->log('crontab launched: ' . date('d.m.Y H:i'));
            $this->on_crontab();
            // loop thru modules
            foreach (self::$modules as $m) {
                $this->crontab($m->get_name());
            }
        } else {
            $module = self::module($module);
            if (is_callable(array($module, 'on_crontab'))) $module->on_crontab();
        }
    }

    /**
     * Parse module langwords into one
     * huge array. Used in templates later.
     * Module lang start with m_
     * [lang.var]
     */
    public function import_langwords($module) {

        $lang      = $this->cfg('lang');
        $lang_file = loader::get_public(loader::DIR_MODULES) . $module . '/' . loader::DIR_LANGS . $lang;

        if (fs::file_exists($lang_file)) {

            $temp = parse_ini_file($lang_file, true);
            //self::dprint('..language ' . $lang_file . " (x" . count($temp) . ")", core::E_DEBUG1);

            if ('core' == $module)
                $this->langwords = array_merge_recursive($this->langwords, $temp);
            else
                $this->langwords['_' . $module] = $temp;
        }
    }

    /**
     * i18n
     * called from render
     * @todo refactor!
     */
    function get_langwords() {
        return $this->langwords;
    }

    /**
     * i18n
     *
     * _T(...) raw text
     *
     * mod\section.string
     * mod.section.string
     *     section.string
     *
     * for translate, use module-based ::translate
     * @param string|array if array passed ['module', 'cont'], otherwise mod=core
     */
    function get_langword($id, $params = null) {

        $mod      = false;
        $first_id = $id;

        // raw text _T(...)
        if (is_string($id) && preg_match('/^_T\((.*)\)$/', $id, $t)) {
            return $t[1];
        }

        $sid = false;

        if (is_array($id)) {
            $mod = $id[0];
            $sid = isset($id[2]) ? $id[1] : false;
            $id  = $sid ? $id[2] : $id[1];
        }

        if (($t = strpos($id, '\\')) || (substr_count($id, '.') >= 2 && ($t = strpos($id, '.')))) {
            $mod = substr($id, 0, $t);
            $id  = substr($id, $t + 1);
        }

        if ($t = strpos($id, '.')) {
            $sid = substr($id, 0, $t);
            $id  = substr($id, $t + 1);
        }

        $return = ($mod && $mod != 'core')
            ? ($sid ? @$this->langwords['_' . $mod][$sid][$id] : @$this->langwords['_' . $mod][$id])
            : ($sid ? @$this->langwords[$sid][$id] : @$this->langwords[$id]);


        if (!$return) {
            core::dprint(
                array('[translate] %s, undefined : %s :: %s :: %s',
                    (is_array($first_id) ? 'array' : print_r($first_id, 1)), $mod, $sid, $id
                ), core::E_NOTICE);
            $return = $id;
        }

        return $return;
    }

    /**
     * Skip console
     */
    static private $_mute_console = false;

    static function mute_console($fl = true) {
        self::$_mute_console = $fl;
    }

    /**
     * вывод текста (::)
     * @param string message
     * @param bool need return or echo
     */

    public static function cprint($buff, $ret = false) {

        if (self::$_mute_console) return;

        $level = false;
        $color = null;

        if (is_array($buff)) {
            $color = $buff['color'];
            $level = $buff['name'];
            $buff  = $buff['text'];
        }
        // $buff = text_proc_c::message_bbdecode($text);
        // $buff = highlight_string($text, true);

        if (loader::in_shell()) {
            echo "console> {$buff}\n";

            return;
        }

        if (!$ret && $con = self::lib('console')) {
            $con->out($buff, $level, $color);
        }

        return $buff;

    }

    /**
     * var dump
     */
    public static function var_dump() {

        // silent in ajax
        if (loader::in_ajax()
            || (!core::debug_level())
        ) return;

        $console  = self::lib('console');
        $dbg_info = '';

        // if no console, trace not available
        if ($console) {
            $dbg_trace = $console->debug_backtrace_smart();
            if (isset($dbg_trace[1]) && !isset($dbg_trace[1]['class'])) $dbg_trace[1]['class'] = '';
            $dbg_info = $dbg_trace[0]['file'] . ' in ' . $dbg_trace[0]['line'] . (!isset($dbg_trace[1]) ? '' : " ({$dbg_trace[1]['class']}::{$dbg_trace[1]['function']}) ");
        }

        $count = func_num_args();
        if (empty($count)) return false;
        $args = func_get_args();

        $i = 0;
        foreach ($args as $p) {
            $i++;
            echo "<code style='color:blue'>VARDUMP #{$i} {$dbg_info}</code>";
            var_dump($p);
        }
    }

    /**
     * Simple trace to
     */
    static function ktrace($loglevel = core::E_TRACE) {
        $console = self::lib('console');
        if (!$console) {
            self::dprint("[STACK] No console available", self::E_ERROR);

            return;
        }

        if (self::debug_level() < $loglevel) return;

        $dbg_trace = $console->debug_backtrace_smart();
        $dbg_trace = array_splice($dbg_trace, 1, 5);
        foreach ($dbg_trace as $trace) {
            $dbg_info = $trace['file'] . ' in ' . $trace['line'] . ' ~ ' . @$trace['class'] . '::' . $trace['function'];
            self::dprint('[STACK] ' . $dbg_info, $loglevel);
        }

        return;
    }


    /**
     * debug level on/off
     */

    public static function set_debug($level) {
        self::$_debug_level = intval($level);
    }

    /**
     * Checks debug level
     * 0 - disabled
     */
    public static function debug_level() {
        return self::$_debug_level;
    }

    /**
     * Is core in debug mode
     * @return bool
     */
    public static function is_debug() {
        return (bool)self::$_debug_level;
    }

    /**
     * bsd rfc 3164
     *   Table 2. syslog Message Severities
     *       0       Emergency: system is unusable
     *       1       Alert: action must be taken immediately
     *       2       Critical: critical conditions
     *       3       Error: error conditions
     *       4       Warning: warning conditions
     *       5       Notice: normal but significant condition
     *       6       Informational: informational messages
     *       7       Debug: debug-level messages
     */


    const E_SQL    = 10;
    const E_FS     = 11;
    const E_NET    = 12;
    const E_RENDER = 13;

    const E_DEBUG0 = 20;
    const E_DEBUG1 = 21;
    const E_DEBUG2 = 22;
    const E_DEBUG3 = 22;
    const E_DEBUG4 = 22;
    const E_DEBUG5 = 22;

    /** 7-TRACE  darkgray   */
    const E_TRACE = 7;
    /** 6-DEBUG  brown      */
    const E_DEBUG = 6;
    /** 5-INFO   green      */
    const E_INFO = 5;
    /** 4-NOTICE darkblue   */
    const E_NOTICE = 4;
    /** 3-WARN   lightred   */
    const E_WARN = 3;
    /** 2-ERROR  black      */
    const E_ERROR = 2;
    /** 1-CRIT   red        */
    const E_CRIT = 1;

    /** Not filterable */
    const E_MESSAGE = 0;

    /** Параметры */
    private static $_debug_config = array(

          self::E_SQL  => array('name' => 'sql', 'color' => '#4082AF')
        , self::E_FS     => array('name' => 'fs', 'color' => 'green')
        , self::E_NET    => array('name' => 'net', 'color' => 'blue')
        , self::E_RENDER => array('name' => 'tpl', 'color' => '#8F8F8F')

          // debug stuff
        , self::E_DEBUG0 => array('name' => 'debug0', 'color' => '#BF6700')
        , self::E_DEBUG1 => array('name' => 'debug1', 'color' => '#EF0E96')
        , self::E_DEBUG2 => array('name' => 'debug2', 'color' => '#8000DF')
        , self::E_DEBUG3 => array('name' => 'debug3', 'color' => '#00DFDB')
        , self::E_DEBUG4 => array('name' => 'debug4', 'color' => '#076F15')
        , self::E_DEBUG5 => array('name' => 'debug5', 'color' => '#7F7400')


          // diagnostics
        , self::E_TRACE  => array('name' => 'trace', 'color' => 'darkgray')
        , self::E_DEBUG  => array('name' => 'debug', 'color' => 'brown') // default
        , self::E_INFO   => array('name' => 'info', 'color' => 'green')
        , self::E_NOTICE => array('name' => 'notice', 'color' => 'darkblue')
        , self::E_WARN   => array('name' => 'warn', 'color' => 'lightred')
        , self::E_ERROR  => array('name' => 'error', 'color' => 'black')
        , self::E_CRIT   => array('name' => 'crit', 'color' => 'red')

        , self::E_MESSAGE   => array('name' => 'info', 'color' => 'green')
    );

    /**
     * Сообщение консоли
     * @param mixed if array passed, then it goes thru vsprinf!
     * @param integer
     * @param mixed false | integer level
     */

    public static function dprint($msg, $level = self::E_DEBUG) {

        if (empty($msg)
            || (!($level == 0 || $level <= self::debug_level()))
        )
            return; // quiet

        if (is_array($msg)) $msg = vsprintf(array_shift($msg), $msg);

        if (!isset(self::$_debug_config[$level])) $level = self::E_TRACE;

        $msg = self::$_debug_config[$level]['name'] . '# ' . $msg;

        $msg = array('text' => sprintf("%6.4f %s", self::ticks(), $msg));
        $msg = array_merge($msg, self::$_debug_config[$level]);

        self::cprint($msg);
    }

    /**
     * dprint_r
     */
    public static function dprint_r($var) {
        self::dprint(print_r($var, true));
    }

    /** временные отметки */
    private static $time_labels = array();

    /**
     * --> time catch
     * Анализ времени выполнения
     * первый вызов- старт, второй - вывод времени
     * @param string     имя метки
     * @param boolean    тихий режим
     * @param boolean    очистить
     * @return integer   затраченное время в мс
     */

    public static function time_check($name = 'default', $b_silent = false, $clear = false) {

        if ($clear && isset(self::$time_labels[$name])) unset(self::$time_labels[$name]);

        if (!isset(self::$time_labels[$name])) {
            self::$time_labels[$name] = microtime(true);
        } else {
            $endtime = microtime(true);
            $time    = sprintf('%.6f', round($endtime - self::$time_labels[$name], 6));
            if (!$b_silent) self::dprint("[timer] {$name} :  " . $time, self::E_TRACE);

            return $time;
        }
    }

    /**
     * start|finish timer
     * @return mixed id/time
     */
    public static function timer($id = null) {
        if (!$id) {
            $id = md5(microtime(true));
            self::time_check($id, true, true);

            return $id;
        } else {
            return self::time_check($id, true);
        }
    }

    /**
     * @return ms from script start
     */
    private static $_ticks = null;

    public static function ticks() {
        if (self::$_ticks === null) self::$_ticks = self::timer();

        return self::timer(self::$_ticks);
    }

    /**
     * Critical shutdown
     */
    function shutdown_critical($msg) {
        @header(' ', true, 500);
        $this->renderer->disable_output(true);
        $msg = $msg ?: 'All your base are belong to us!';
        echo $msg;
        die;
    }

    /**
     * @param $msg
     * @param int $code
     * @return $this
     */
    function error($msg, $code = 500) {

        @header(' ', true, $code);

        $error = i18n::T('errors.' . $code);

        $template = loader::in_ajax() ? 'partials/error' : 'error';

        $this->renderer->set_page_title($error)
            ->set_return('error', array('message' => $error, 'code' => $code))
            ->set_return('message', $msg)
            ->set_main_template($template);

        $this->renderer->output();

        $this->halt();
    }

    /**
     * Server 404 Error - page not found
     */
    function error404($msg) {
        return $this->error($msg, 404);
    }

    /**
     * Server 500 Error
     */
    function error500($msg) {
        return $this->error($msg, 500);
    }

    /**
     * Auth errors
     */
    function error401($msg) {
        return $this->error($msg, 401);
    }

    /**
     * @deprecated use renderer methods
     *
     * Set raw message
     * @see self::set_message()
     */
    function set_raw_message($value, $append = false, $separator = '<br/>') {
        if (is_array($value)) $value = vsprintf(array_shift($value), $value);
        if (!empty($this->message) && $append)
            $this->message .= ($separator . $value);
        else
            $this->message = $value;
    }

    /**
     * @deprecated use renderer methods
     *
     * @todo refactor
     * Setup main message
     * (if something not ordinary occured)
     *
     * Automatically passed to template in
     * @see renderer::render_message
     *
     * @param string langID
     * @param array|string varargs parsed against $lang_id
     * @param bool $vars - is langwords keys
     * @return self
     */
    function set_message($lang_id, $vars = array(), $is_langs = false, $separator = '<br/>') {

        $value = $this->get_langword($lang_id);
        if (empty($value)) {
            $value = '*lang=' . $lang_id;
        }
        if (!empty($vars)) {
            if ($is_langs)
                foreach ($vars as &$var) {
                    $var = $this->get_langword($var);
                }
            $value = vsprintf($value, $vars);
        }

        // remove unused printf %sequences
        $value = preg_replace('#(\s\%[^\s]+)#', '', $value);

        // append messages
        if (!empty($this->message))
            $this->message .= ($separator . $value);
        else
            $this->message = $value;

        return $this;
    }

    /**
     * @deprecated use renderer methods
     * Get global message
     */
    function get_message() {
        return $this->message;
    }

    /**
     * @deprecated use renderer methods
     *
     * Set message data
     * visible in template is 'message_data'
     *
     * Call this method if you want signal that message is error
     *
     * (frontend)
     *
     * @return self
     */
    function set_message_data($data, $is_error = false) {
        $this->message_data = $data ? $data : array();
        if (is_array($this->message_data) && !isset($this->message_data['status'])) $this->message_data['status'] = !$is_error;

        return $this;
    }

    function get_message_data() {
        return $this->message_data;
    }

    /**
     * Set/get last modified time
     */
    function last_modified($time = false) {
        if ($time) $this->_last_modified = $time;

        return $this->_last_modified;
    }

    /**
     * Check last modified time and
     * do some action
     */
    function check_last_modified() {
        $user = $this->lib('auth')->get_user();

        $disable = $this->cfg('disable_last_modify', false);

        if (!$disable && $user->is_anonymous()) {
            if ($time = $this->last_modified()) {
                $last_modified = date('r', $time);
                @header('Last-Modified: ' . $last_modified);
                @header('Cache-Control: max-age=3600, must-revalidate');
            }
        } else {
            // dont cache user
            functions::headers_no_cache();
        }
    }

    /**
     * Run event
     * Delegate this on each module
     */
    static function event($name, $parms = array()) {
        self::dprint('[CORE::EVENT] ' . $name);
        self::modules()->event($name, $parms);
    }

    /**
     * Get main domain
     * On FreeBSD SERVER_NAME include subdomains, on windows not
     * The idea is to put "main_domain" into config
     *
     * Warn! From shell SERVER_NAME undefined, set main_domain in engine.cfg
     */
    function get_main_domain($prefix = false) {
        $domain_prefix = $this->cfg('domain_prefix', ''); // 'www.'
        return (
            ($prefix ? $domain_prefix : '')
            . (($md = $this->cfg('main_domain')) ? $md : @$_SERVER['SERVER_NAME'])
        );
    }

    /**
     * Set main domain
     * @param $domain
     */
    function set_main_domain($domain) {
        $this->set_cfg_var('main_domain', $domain);
        return $this;
    }

    /**
     * @return ctype_item
     */
    function get_ctype($id, $is_model = true) {

        // if not initialized yet
        if (!$id || !isset($this->_ctypes)) return false;

        if ($is_model)
            return $this->_ctypes->get_item_by_model($id);
        else
            return $this->_ctypes->get_item_by_id((int)$id);
    }

    /** @return ctype_collection */
    function get_ctypes() {
        return $this->_ctypes;
    }

    /**
     * @see self::get_ctypes()
     * @return ctype_collection
     */
    protected function get_ctype_handle() {
        return $this->class_register('ctype');
    }

    /* core collections */

    /** @return bans_collection */
    function get_bans_handle() {        return $this->model('bans');    }

    /** @return logs_collection */
    function get_logs_handle() {        return $this->model('logs');    }

    /** @return texts_collection */
    function get_texts_handle() {       return $this->model('texts');    }

    /** @return mail_tpl_collection */
    function get_mail_tpl_handle() {    return $this->model('mail_tpl'); }

    /**
     * Get texts content
     * @return texts_item text
     *
     */
    function get_text($id) {

        $cache_id = $id; // all hack
        $data     = $this->lib('manager')->get('texts', $cache_id);

        if (null !== $data) return $data;

        $handle = $this->get_texts_handle()
            ->set_where("name = '%s'", $id)
            ->set_limit(1)
            ->load()
            ->get_item();

        $this->lib('manager')->set('texts', $cache_id, $handle);

        return $handle;
    }

    /**
     * Get dynamic config handle
     * @return config_collection
     */
    function get_dyn_config() {
        return $this->dyn_config;
    }

    /**
     * Get renderable stuff
     */
    function get_renderable_config() {

        $return = array();

        // old format via config
        $_s = $this->cfg('render_config_vars', '');

        if ($_s) {
            $_s = explode(',', $_s);

            foreach ($_s as $key) {
                $key          = trim($key);
                $return[$key] = $this->cfg($key);
            }
        }

        // dyn config id{name,title,value}
        foreach ($this->dyn_config as $item) {
            if (!$item->b_system) {
                $return[$item->name] = $item->render();
            }
        }

        return $return;
    }

    /**
     * Get url for static content
     * @return string domain url
     */
    function get_static_url() {
        return ($t = $this->cfg('static_domain')) ? "http://{$t}" : '';
    }

}

// query counter
core::ticks();


/**
 * i18n shortcut
 */
class i18n {

    /**
     * i18n translate
     * automaticaly pass module
     * call to core::get_langword($id, $mod)
     */
    static function T($id, $params = null) {
        return core::get_instance()->get_langword($id, $params);
    }
}