<?php
/**
* @package TwoFace
* @version $Id: module_blocks.php,v 1.5.2.1.2.1 2012/05/16 08:31:33 Vova Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/

/**
 * Class module_block
 */
abstract class module_block {

    public $title;
    public $template;

    /** @var core_module */
    protected $context;

    protected $block;
    protected $params;

    /**
     * @param module_blocks $p
     * @param null $params
     */
    function __construct($context, &$block, $params) {

        $this->context = $context;
        $this->block = &$block;
        $this->params = $params;
        $this->construct_after();
    }

    function construct_after() {}

    function set_template($t) {
        $this->block['template'] = $t;
    }

    function set_title($t) {
        $this->block['title'] = $t;
    }

    /**
     * Get passed param by name
     */
    function get_param($key, $default = null) {
        return isset($this->_params[$key]) ? $this->_params[$key] : $default;
    }

    /**
     * Entry point
     * @return data for block
     */
    abstract function run();

}

/**
* Module blocks
* @package core      
*/ 
class module_blocks {
    
    /** @var core_module base object */
    protected $context;
    
    /**
    * Build new one
    * @param object to interact with
    */
    function __construct($context) {
        $this->context = $context;
    }
    
    /**
    * Context
    * @return core_module
    */
    function get_context() {
        return $this->context;
    }
    
    /**
    * Registered blocks
    * ['action'] => {
    *   'template'  => blocks/{template}.tpl
    *   'title'     => title
    * }
    */
    protected $_blocks = array();

    /**
    * get registered block
    * @return array    
    */
    public function get_block($action) {
        return isset($this->_blocks[$action]) ? $this->_blocks[$action] : false;
    }
    
    /**
    * Run block
    * Called from module::run_block
    * 
    * Params passed to 'block.data' 
    * action converted to object!
    * 
    * Extended params passed to {$block.params}
    * 
    * @param string $action action-name
    * @param array $params passed to {satblock}
    * @return bool|string
    * @throws modules_exception
    */
    function run($action, $params) {

		$modname = $this->get_context()->get_name();

        core::time_check('block', 1, 1);
        core::dprint('[BLOCK ' . $modname . '.' . $action . ']' , core::E_DEBUG);

        // get block builtin properties
        $props = $this->get_block($action);

        // no block description found, try from file

        if (!$props) {
            $props = array('class' => true);
        }

        // assign params
        $props['params'] = $params;

        $_params = new aregistry($params);

        if (is_callable(array($this, $action))) {

            $data = call_user_func(array($this, $action), $_params);

        }
        elseif (!empty($props['class'])) {

            $action = is_string($props['class']) ? $props['class'] : $action;

            // try external file
            $file = $this->get_context()->get_root() . 'blocks/' . $action . loader::DOT_PHP;

            if (file_exists($file)) {
                require $file;
                $class = core_modules::ns($this->get_context()->get_name(), $action . '_block');
                if (!class_exists($class)) {
                    throw new module_exception('Class not exists - ' . $class);
                } else {
                    $data = with(new $class($this->get_context(), $props, $_params))->run();
                }
            }
            else {
                throw new module_exception('Block file not found - ' . $action);
            }

        } else {
            throw new module_exception('Not callable action supplied - ' . $action);
        }

        $return = $this->get_context()->renderer->render_block($props, $data);

		core::dprint('[/BLOCK]' . ' ' . core::time_check('block', 1)  , core::E_DEBUG);

        return $return;
    }
    
}
 
