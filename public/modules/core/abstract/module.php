<?php
 
/**
* @package TwoFace
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/

/**
* Module class
*
* @property module_controller           $controller
* @property module_router               $router
* @property module_blocks               $blocks
*
* @property tf_auth                     $auth
* @property tf_request                  $request
* @property tf_renderer                 $renderer
* @property tf_logger                   $logger
* @property Smarty3                     $tpl_parser
* @property dbal                        $db
* @property tf_editor                   $editor
* @property Debug_HackerConsole_Main    $console
*
* @property core                        $core
*/
abstract class core_module extends module_orm {

    /**
     * @var array content-type declarations for module (see config/ctype.cfg)
     */
    protected $ctypes;

    /** @var tf_manager moved to ioc */
    // protected $manager;

    /** routes */           protected $routes;

    /** Module root dir */  public $root_dir;
    /** Models root */      public $classes_chroot;
    /** Module name */      public $name;
    /** module URL */       public $base_url;

    /** current router */   private $_is_router = false;

    /**
    * Filter. Set up in router/controller for loading bulk of items
    * modified for their needs.
    * @see self::set_filter()
    */
    protected $filter_config   = array();
    
    /** 
    * Флаг инициализации 
    * Установить true, если модуль инициализирован для всех уровней ($level=0-10)
    * {@link core_c::modules_init}
    */
    public $initialized = false;    
    
    /**
    * Default editor action,
    * if empty tag is used
    */
    protected $editor_default_action; 

    /** module aliases */
    protected $_aliases = array();

    /** @var  module_ioc */
    protected $ioc;

    const IS_LOADING = -1;

    /**
     * Default deps
     * @return array
     */
    function IOC_initialize() {

        // class assigned in init0

        return array(

            'manager'    => array('class' => '', 'instance' => null, 'require' => false),
            'controller' => array('class' => '', 'instance' => null, 'require' => false, 'fallback' => 'module_controller'),
            'router'     => array('class' => '', 'instance' => null, 'require' => false, 'fallback' => 'module_router'),
            'blocks'     => array('class' => '', 'instance' => null, 'require' => false, 'required' => false),

            /*
             //renderer already defined in core::libs
            'renderer'   => array(
                'class' => function () { return core::lib('renderer'); }
                , 'instance' => null, 'require' => false
            ),

            'validator'   => array(
                'class' => function () {
                        $class = $this->ns('Validator');
                        return new $class;
                    }
                , 'instance' => null
                , 'require' => false
            ),
            */

            'core'  => array(
                'class' => function() { return core::selfie(); }
            )

         );

    }

    /**
    * Инициализируем класс (конструктор)
    * Вызывыем из дочернего класса
    * 
    * @param string физ.путь до модуля
    */     
    public function __construct($root_dir = null, $params = null) {    
        
        if (empty($root_dir)) throw new module_exception('empty root, register ' . get_class($this));
        
        $this->root_dir         = $root_dir;
        $this->classes_chroot   = $root_dir . 'classes/';
        
        $class = get_class($this);
        
        // if not core, get its instance
        if ($class != strtolower('core')) {
            
            $this->name  = !isset($params['name'])
                ? substr($class, strlen(loader::CLASS_PREFIX))
                : $params['name'];
                
            $this->core = core::selfie();

            // alloc manager
            // todo: why clone?
            $this->manager = clone core::lib('manager');
        }
        else {
            $this->name = $class;
        }
        
        if (!empty($params['alias'])) {
            // @todo trim
            $this->_aliases = explode(',', $params['alias']);
        }    

        // append config|create
        $this->init_config($params, true);

        $this->ioc = new module_ioc($this->IOC_initialize(), $this);

        $this->construct_after();

        $this->_register_ctypes();
    }
    
    function construct_after() {}

    /**
     *  $ctypes []= array(
     *   'id'    => 402,
     *   'model' => 'test.article_category'
     *   );
     */
    private function _register_ctypes() {

        if (!empty($this->ctypes)) {
            $collection = $this->core->get_ctypes();

            foreach ($this->ctypes as $ctype) {
                $collection->append(
                    $collection->alloc($ctype)
                );
            }
        }
    }
    
    /**
    * Get module root dir
    */
    function get_root() {
        return $this->root_dir;
    }    
    
    /** @return tf_manager */
    function get_manager() {
        return $this->manager;
    }
    
    /**
    * @return bool is alias
    */
    function is_alias($a) {
        return in_array($a, $this->_aliases);
    }
    
    /**
    * Get a core
    * @return core
    */
    function get_core() {
        return ($this instanceOf core) ? $this : $this->core;
    }

    /** 
    * get module name (tag) 
    */
    function get_name() {
        return $this->name;
    }
    
    /**
    * toString
    */      
    public function __toString() {
        return $this->name;
    }
    
    /**
    * Действие, обработчик событий 
    * админ панели
    * @param string on_{метод}
    * @param array параметры
    */
    /*
    public function on_editor_action($action, $parms=array()) {
        
        $action = "on_editor_{$action}";

        if (method_exists($this, $action)) {
            call_user_func(array($this, $action), $parms);
        }
        
        $menu = require ($this->root_dir . "editor/menu.php");                         

        
    }
    */
    
    /**
    * Get editor base url 
    */
    public function get_editor_base_url() {
        return ('?' . ident_vars::IDENT_MODULE . '=' . $this->name
              . '&' . ident_vars::IDENT_CONTROLLER . '=' . core::get_params(ident_vars::IDENT_CONTROLLER));
    }
    
    /**
    * append base url
    */
    function append_base_url($part, $finish = false, $start = false) {
        $this->base_url .= (($start ? '/' : '') . $part . ($finish ? '/' : ''));
    }
    
    /**
    * Return URL for current engine state
    */
    function get_base_url() {
        return $this->base_url;
    }

    
    /**
    * Set filter configuration
    * Used in controller to configurate module filter
    * @param mixed
    * @param string key, if single key
    */
    function set_filter_config($filter, $value = false) {
        if (is_string($filter))
            $this->filter_config[$filter] = $value;
        else
            $this->filter_config = $filter;
    }
    
    /** 
    * Модуль вызывается в {@see core_c::modules_render}  
    * Должен присутствовать метод module::render($tpl_parser)
    * проверить на core::in_editor()  
    */
    function is_renderable() {
        return method_exists($this, 'render');
    }   
    
    /**
    * Неактивный вывод модуля в шаблон
    * Вызывается из renderer::render_modules
    */      
    // function render($renderer) 
    
    /**
    * assign controller 
    */
    private function assign_controller($object) {
        $this->controller = $object;
    }

    /**
    * get controller 
    */
    public function get_controller() {
        return $this->controller;
    }
        
    /**
    * Get routes
    * If not set, router tries to it load from file
    */
    function get_routes() {
        return isset($this->routes) ? $this->routes : false;
    }    
        
    /**
    * первичная инициализация модуля (core::init(0) -> core::init0() >- this::init0())
    * 
    * {@see core_c::modules_init}
    * @throws tf_exception
    */       
    function init0() {
        
        $this->get_core()->import_langwords($this->name);

        // include exception  for module
        // do not load core exception twice (when compressed core used)
        $inc = $this->root_dir . 'exceptions' . loader::DOT_PHP;
        if (!class_exists($this->get_name() . '_exception', 0) && fs::file_exists($inc)) {
            require_once $inc;
        }
        
        //
        // allocate router

        $this->_create_router();
            
        //
        // controller

        $this->_create_controller();
            
        //
        // blocks

        $this->_create_blocks();

        if (is_callable(array($this, 'init_after'))) $this->init_after();
                
    }

    /**
     * @param null $alt_class
     */
    protected function _create_blocks($alt_class = null) {

        $blocks_file = $this->root_dir
            . ($alt_class ? "../{$alt_class}/users/" : '')
            . 'blocks' . loader::DOT_PHP;

        $blocks_class = ($alt_class ? "{$alt_class}_" : '') . $this->name . '_blocks';


        $this->ioc->dependencies['blocks']['class']   = $blocks_class;
        $this->ioc->dependencies['blocks']['require'] = $blocks_file;
        $this->ioc->dependencies['blocks']['params']  = array($this);
       
    }

    /**
     * @param null $alt_class
     */
    function _create_controller($alt_class = null) {

        $controller_file = $this->root_dir
            . ($alt_class ? "../{$alt_class}/users/" : '')
            . 'controller' . loader::DOT_PHP;

        // class must be named {module}_router
        $controller_class = ($alt_class ? "{$alt_class}_" : '') . $this->name . '_controller';

        // core::dprint(array('..controller %s, %s', $controller_class, $controller_file), core::E_DEBUG2);

        $this->ioc->dependencies['controller']['class']   = $controller_class;
        $this->ioc->dependencies['controller']['require'] = $controller_file;
        $this->ioc->dependencies['controller']['params']  = array($this, core::lib('renderer'));

    }

    /**
     * @param null $alt_class
     */
    protected function _create_router($alt_class = null) {
        $router_class = '';

        $router_file = $this->root_dir
            . ($alt_class ? "../{$alt_class}/users/" : '')
            . 'router' . loader::DOT_PHP;

        // class must be named {module}_router
        $router_class = ($alt_class ? "{$alt_class}_" : '') . $this->name . '_router';

        $this->ioc->dependencies['router']['class']   = $router_class;
        $this->ioc->dependencies['router']['require'] = $router_file;
        $this->ioc->dependencies['router']['params']  = array($this);
    }
    

    // for the future!
    /*
    public function init2() {}
    public function init3() {}
    public function init4() {}
    public function init5() {}
    public function init6() {}
    public function init7() {}
    public function init8() {}
    */
    
    /**
    * Завершающий init 9
    * Called on module shutdown
    */
    public function init9() {
        if (is_callable(array($this, 'shutdown_after'))) $this->shutdown_after();
    }
    
    function init10() {
        if (is_callable(array($this, 'halt_after'))) $this->halt_after();
    }
    
    /**
    * Вызывается роутером при передаче управления модулю
    * с фронта
    */      
    public function main() {
        
        core::dprint('MAIN:' . $this->name);
        
    }
    
    /**
    * assign router object
    */
    private function assign_router($object) {
        $this->router = $object;
    }
    
    /**
    * get router object
	* @return module_router
    */
    public function get_router() {
        return $this->router;
    }
    
    /**
    * Set this if module is router
    */
    function set_is_router($bool = true) {
        $this->_is_router = $bool;
    }         
    
    /**
    * Router?
    * @return bool
    */
    function is_router() {
        return $this->_is_router;
    }
    
    /**
    * On crontab
    * Run scripts from crontab folder of module if exists
    */
    function on_crontab() {
        $actions = "{$this->root_dir}crontab/*.php";   
        $files = glob($actions);
        
        if (!empty($files)) {
            foreach ($files as $file) {
                echo "Run : {$file} \n";
                require $file;  
            }
        }    
    }

    protected $editor_actions;

    /**
     * Get edit actions list
     * @return mixed
     */
    function get_editor_actions() {

        if (!isset($this->editor_actions)) {
            $actions_file = "{$this->root_dir}editor/actions.php";

            if (file_exists($actions_file)) {
                $this->editor_actions = require $actions_file;
            }

            if (!empty($this->editor_actions)) {
                foreach ($this->editor_actions as $key => &$action) {

                    if (!isset($this->editor_default_action) && !empty($action['default'])) {
                        $this->editor_default_action = $key;
                    }

                    if (!isset($action['title']) && is_string($key)) {
                        $action['title'] = i18n::T($this->get_name() . '.' . $key);
                    }
                }
            }
        }

        return $this->editor_actions;
    }
    
    /**
    * on editor action
    * 
    * Called in editor to module
    * 
    * @return array(
    *   'layout'                = false
    *   'template'              = false
    * )
    */ 
    function on_editor() {

        core::lib('editor')->assign_module_menu(
            $this->get_editor_actions()
        );

        // Controller action
        $controller_action = '';   

        // modify ident var 'c' (if empty)
        if (empty(core::get_params()->c) && !empty($this->editor_default_action)) {
            core::get_params()->c = $this->editor_default_action;
        }
        
        $controller_action = preg_replace('/[^a-z\d\_]/i', '', core::get_params()->c);

        core::dprint("on_editor controller: " . $controller_action);

        if (empty($controller_action)) {
            throw new editor_exception('Empty action');
        }
        
        // check user has access
        core::lib('editor')->on_editor($this);

        // dispatch

        if (!empty($controller_action)) {
            $controller_action_file = $this->root_dir . "editor/controllers/{$controller_action}" . loader::DOT_PHP;
            if (fs::file_exists($controller_action_file, false)) {
                
                require $controller_action_file;
                
                // run controller object, if present
                $controller_class = $this->get_name() . '_' . $controller_action . '_controller';
                
                if (class_exists($controller_class, 0)) {
                    core::dprint("run controller object : {$controller_class}");

                    /** @var editor_controller $controller */
                    $controller = new $controller_class($this);

                    $controller->run();

                    $layout   = $controller->get_layout();
                    $template = $controller->get_template();

                    $this->renderer
                        ->set_page_template($layout)
                        ->set_main_template($template);

                } else {
                    throw new core_exception('Bad controller class name ' . $controller_class);
                }
                
            }
            else core::dprint('[ERROR] Unable to execute ' . $controller_action_file);
        }

        /*  @return main template
        */          
        /*
        return array(
            'layout'    => $layout,
            'template'  => $template
        );
        */
    }

    /**
     * Get lib (IOC)
     * $module->renderer etc.
     * @param $key
     */
    function __get($key) {

        if (isset($this->ioc->dependencies[$key])) {
            return $this->ioc->resolve_dependency($key);
        }

        return core::lib($key);
    }


    /**
    * i18n alias
    */
    function T($id, $params = null) {
        return $this->translate($id, $params);
    }   
    
    /**
    * Translate
    * @param mixed $id
    */
    public function translate($id, $params = null) {
        return $this->get_core()->get_langword(array($this->get_name(), $id), $params);
    }
    
    /**
    * On Event handler
    * Call on_{event} method with $params
    * 
    * @param string event name
    * @param array params
    * @return mixed result
    */
    public function trigger($name, $params) {
        $method = 'on_' . $name;

        return (is_callable(array($this, $method)))
            ? call_user_func(array($this, $method), $params)
            : false
            ;
    }
    
    
    /**
    * assign blocks object
    */
    private function assign_blocks($object) {
        $this->blocks = $object;
    }
    
    /**
    * Run block stuff
    * @return html
    */
    public function run_block($action, $params) {
        return $this->blocks->run($action, $params);
    }
    
    /** 
    * Get managed model item
    * @return abs_collection_item
    */    
    public function get_managed_item($model, $id) {

        // check if model prefixed
        if (strpos($model, '.') !== false) {
            $_model = explode('.', $model);

            if ($_model[0] !== $this->get_name()) {
                return core::module($_model[0])->get_managed_item($_model[1], $id);
            }

            $model = $_model[1];
        }

        // prefix module.model
        $_model = 'model.' . $this->get_name() . '.' . $model;

        $item = $this->manager->get($model, $id);

        if ($item === null) {
            $item = $this->model($model)->load_only_id($id);
            $this->manager->set($_model, $id, $item);
        }

        return $item;
    }    
        
}
