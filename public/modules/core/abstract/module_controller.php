<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: module_controller.php,v 1.8.2.4.2.14 2013/05/15 07:19:31 Vova Exp $
 */

/**
 * Module controller
 *
 * @property module_router               $router
 * @property module_blocks               $blocks
 *
 * @property tf_auth                     $auth
 * @property tf_request                  $request
 * @property tf_renderer                 $renderer
 *
 * @property core                        $core
 */
class module_controller {  
    
    /**
    * Req parts
    */
    protected $req; 
    
    /**
    * Current data
    */

    /** @var  abs_collection */
    protected $_current_collection;

    /** @var  abs_collection_item */
    protected $_current_item;

    /**
    * Parent module
    * @var core_module 
    */
    protected $context;

    protected $_action;
    protected $_params;
    protected $_template;
    protected $_layout;
    protected $_section;
    protected $_title;

    /**
    * This is action name for export to renderer
    */
    private $action_name   = '';    
    private $section_name  = '';   

    
    /**
    * Construct
    * @param core_module
    */
    public function __construct($context) {
        $this->context = $context;
        $this->construct_after();
    }
    
    protected function construct_after() {}
    
    /**
    * @return core_module
    */
    public function get_context() {
        return $this->context;
    }
    
    /**
    * @return tf_renderer
    */
    public function get_renderer() {
        return $this->renderer;
    }   
    
    /**
    * @return users_item 
    */
    public function get_user() {
        return $this->auth->get_user();
    }  
    
    /**
    * @return module_router
    */
    public function get_router() {
        return $this->router;
    }     
    
    /**
    * Run action
    * Called from @see module_router::route()
    * 
    * params
    * ------
    * action
    * section
    * template
    * file          - file _ parsed to /
    * _file         - raw file
    * 
    */
    function run($route, $params = null) {
        
        $this->_action    = $route['action'];
        $this->_params    = new aregistry($params);
        $this->_section   = @$route['section'];
        $this->_title     = @$route['title'];
        $this->_layout    = @$route['layout'];

        $section_path     = '';
        // $section_path = isset($route['section']) ? "{$route['section']}/" : '';
        
        // append section and module name
        $this->_template = !empty($route['template'])
            ? ($this->get_context()->get_name() . '/' . $section_path . $route['template'])
            : false;

        // run section
        if (!empty($this->_section)) {
            $this->run_section($this->_section, $route, $params);
        }

        $response = null;
        
        // run action

        if ($route['type'] == 'inline') {
            // closure
            $response = call_user_func($this->_action, $this);
        } else
        if ($route['type'] == 'method') {
            // method in controller.file
            if (!empty($this->_action)) {
                $method = 'action_' . /*(!empty($route['section']) ? ($route['section'] . '_') : '') .*/ $this->_action;
                if (!method_exists($this, $method)) {
                    throw new router_exception('Action method not found ' . $this->_action . ' | ' . $method);
                }
                $response = call_user_func(array($this, $method));
            }
        }
        else
        if ($route['type'] == 'class') {
            // external actions/file
            $class_file   = isset($route['file']) ? $route['file'] : $this->_action;
            $class_file   = $section_path . $class_file;
              
            $_action = array('action' => $this->_action, 'file' => $class_file);            
            if (isset($route['_file'])) $_action['_file'] = $route['_file'];

            $response = $this->run_file_action($_action);
        }

        if ($response && $response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }
        
        core::dprint(array("controller::run(%s) type: %s", $this->action_name, $route['type']));
        
        // set title
        if (!empty($this->_title)) {
            $this->renderer->set_page_title($this->_title);
        }

        // set body template
        $this->renderer->set_main_template($this->get_template());   

        // page layout
        if (!empty($this->_layout)) {
            $this->renderer->set_page_template(
                // @todo unhardcode, move to renderer method
                'pages/' . $this->_layout
            );
        }
                 
        
    }
    
    /**
    * run section
    */
    function run_section($section) {
         $method = 'section_' . $section;
         if (method_exists($this, $method)) {     
            core::dprint(array("[CONTROLLER::SECTION] %s", $section));        
            call_user_func(array($this, $method));
         }
    }

    /**
    * Run action from file    
    * @param array|string array('action', 'file')
    * @param mixed for testing, otherwise controller params used
    */
    function run_file_action($name, $params = null) {

        $action  = '';
        $file = '';
        $is_raw_file = false;
        
        if (is_array($name)) {
            $action = $name['action'];   
            $file   = $name['file'];            
            if (isset($name['_file'])) { $file = $name['_file']; $is_raw_file = true; }           
        }
        else {
            $file = $action = $name;
        }

        /*
        if (!empty($name['section'])) {
            $action = $name['section'] . '/' . $action;
        }
        */

        $action = str_replace('/', '_', $action);
        $fname = $file; //$is_raw_file ? $file : str_replace('_', '/', $file);
         
        $file = $this->get_context()->get_root() . 'actions/' . $fname . '.php';
        
        if (!file_exists($file)) throw new controller_exception('Action file not found ' . $file);
        
        fs::req($file);
        
        $class = $action . '_action';
        $class = $this->core->modules()->ns($this->context->get_name(), $class);
        
        core::dprint(array("[CONTROLLER::RUN_FILE] %s from %s", $class, $file));
        
        if (!class_exists($class, 0)) throw new router_exception('Action class not exists ' . $class);
        
        if (empty($params)) $params = $this->_params;
        
        $action = new $class($this, $params);

        return $action->run();
    }
    
    /**
    * Direct to renderer
    * 
    * @param mixed $t
    * @param mixed $append
    */
    function set_title($t, $append = true) {
        $t = $this->context->T($t);
        $this->renderer->set_page_title($t, $append);
    }
    
    function _set_title($t) {
        $this->_title = $t;
        return $this;
    }
    
    /**
    * Vsprintf curernt route title
    * @param vararg
    */
    function set_title_params() {
        $params = func_get_args();
        if (!empty($this->_title)) {
            $this->_set_title(
             vsprintf($this->_title, $params)
            );
        }
        return $this;
    }    
    
    /**
    * Export action
    * Value sets in @see self::action()
    */
    public function get_action_name() {
        return $this->action_name;
    }
    
    /**
    * Set action name for tpl
    */
    public function set_action_name($name) {
        $this->action_name = $name;
    }
    
    /**
    * Export section
    */
    public function get_section_name() {
        return $this->section_name;
    }
    
    /**
    * Set section name for tpl
    * Current section (faculty, category, user_cp, ...)
    */
    public function set_section_name($name) {
        $this->section_name = $name;
    }

    function get_title() {
        return $this->_title;
    }
    
    /**
    * Assign action (will run at end of route)
    * Sets by @see self::check_action()
    * 
    * @param string
    * @param mixed
    */
    protected function assign_action($action, $params = false) {
        $this->_action = $action;
        $this->_params = $params;
    }
    
    /**
    * Get assigned action
    */
    protected function get_action() {
        return $this->_action;
    }
    
    /**
    * Get assigned action params
    */
    protected function set_params($p) {
        $this->_params = $p;
    }    
    
    /**
    * Get assigned action params
    */
    protected function get_params() {
        return $this->_params;
    }

    /**
    * Get passed param by name
    */
    function get_param($key) {
        return isset($this->_params[$key]) ? $this->_params[$key] : null;
    }
    
    /**
    * Get tpl for action
    */
    public function get_template() {
        return $this->_template;
    }

    /**
    * Set tpl for action 
    * Overrides controller template
    * Used by ajax queries and other
    */
    function set_template($tpl) {
        $this->_template = $tpl;
    }    
    
    /**
    * Set empty template
    */
    function set_null_template() {
        $this->_template = false;
    }  
    
    function set_layout($l) {
        $this->_layout = $l;
    }
    
    function get_layout() {
        return $this->_layout;   
    }
        
    /**
    * @abstract class
    * Check for action in url parts
    * Trim them
    */
    
    public function check_action(&$parts) {          
    }
    
    /**          
    * @deprecated
    * 
    * Do Action
    * 
    * assign thru @see self::assign_action()
    * alternative submit in POST['action']
    * 
    * All template manipulations thru @see self::set_template @see self::get_template
    * 
    * @param object request object
    * @param &string allow modify template template   
    * 
    * @return string template
    */
    public function action($r) { //, &$template) {
        
        $_action = $this->get_action();        
        $action = (empty($_action) && isset($_POST['action'])) ? $_POST['action'] : $_action;
        
        $action = preg_replace('/[^a-z_]/', '', $action);

        if (empty($action)) {
            core::dprint('Empty action');
            return;
        }
        
        if (is_callable(array($this, $action))) {
            call_user_func(array($this, $action));
        }
        else {
            core::dprint('Calling not callable action ' . $action . ' in ' . $this->get_context()->get_name() . ' controller');
        }
        
       
        return $this->get_template();
    }
    
    
    /**
    * Set req parts
    * (sets on self::run()) manually
    * Used for set extended params, passed in url
    */
    protected function set_req($r) {
        $this->req = $r;
    }
    /**
    * get req parts
    * 
    * @see set_req() in @see run()
    * 
    * @param string id of param or false if all object needed 
    */
    public function get_req($id = null) {
        if (!isset($id)) return $this->req;
            
        //$prop = new ReflectionProperty('stdClass', $id);                
        //return $prop->getValue($this->req);
        
        $vars = get_object_vars($this->req);
        
        return (isset($vars[$id])) ? $vars[$id] : false;        
    }


    /**
     * Set current collection
     * @param $data
     * @return $this
     */
    function set_current_collection($data)  { $this->_current_collection = $data; return $this; }
    function get_current_collection()       { return $this->_current_collection;  }

    /**
     * Set current item
     * @param $data
     * @return $this
     */
    function set_current_item($data)        { $this->_current_item = $data; return $this; }
    function get_current_item()             { return $this->_current_item;  }

    function get_request() { return $this->request; }

    /**
     * Query context (IOC)
     */
    function __get($key) {
        return $this->context->$key;
    }

    
}

/**
 * External action class
 *
 * @property tf_auth                     $auth
 * @property tf_request                  $request
 * @property tf_renderer                 $renderer
 */
abstract class controller_action {

    /** @var module_controller */
    protected $controller;

    /** @var array external params */   protected $_params;    
    /** @var core_module */             protected $context;

    /**
     * @param module_controller $p
     * @param null $params
     */
    function __construct($p, $params = null) {
        $this->controller = $p;
        $this->_params = $params;
        $this->context  = $p->get_context();

        $this->construct_after();
    }

    function construct_after() {}

    /**
    * Get passed param by name
    */
    function get_param($key) {
        return isset($this->_params[$key]) ? $this->_params[$key] : null;
    }    
    
    /**
    * Title to renderer
    */
    function set_title($t, $a = true) {
        $this->controller->set_title($t, $a);
    }

    /**
     * Call section
     * @param $section
     * @param $route
     * @param $params
     */
    function run_section($section, $route, $params) {

        $method = "section_{$section}";
        if (functions::is_callable(array($this, $method))) {
            $this->$method($route, $params);
        }

    }
    
    /**
    * Called by controller
    */
    abstract function run();

    /**
     * Query context (IOC)
     */
    function __get($key) {
        return $this->context->$key;
    }
}

