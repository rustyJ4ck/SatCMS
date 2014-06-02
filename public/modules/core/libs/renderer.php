<?php

/**
 * Renderer
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: renderer.php,v 1.11.2.4.2.8 2012/12/18 19:37:10 j4ck Exp $
 */

/**
 * Class tf_response
 */
class tf_response {

    protected $template = null;
    protected $buffer = null;

}

require "modules/core/libs/renderer/layout.php";

 /**
 * 
 * Basic templating stuff
 * ----------------------
 * 
 * All data vars (setted thru set_data)
 * goes to template as root variables
 * 
 * [data] (root-level)
 * 
 * main_template
 * modtpl_prefix     - module template path (editor chroot)
 * 
 * message           - message (core::set_message)
 * message_data      - message_data (core::set_message_data)
 * 
 * [cp]              - mod_users
 *      links
 * 
 * [user]            - active user
 *
 * ident_vars deprecated - use req
 * [req]      - core::get_params
 * 
 * [config]          - renderer data array + core config
 *      static_url      - domain for static content
 *      template_url    - active template url
 *      site_url        - site url with trailing /
 *      in_ajax         - in ajax flag
 *      in_index        - index page
 *      module          - active module
 *      action          - action (sets via controller::set_action_name)
 *      section         - section (sets via controller::set_section_name)
 *      uri             - $_SERVER['REQUEST_URI']
 * 
 * [lang]            - array with current langwords
 */
 
class tf_renderer extends abs_data {
    
    private $tpl_parser;

    private $template_url;
    private $page_template  = 'root';
    private $page_title     = array();

    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_HTML = 'text/html';
    
    private $_content_type = 'text/html';
    private $_charset = 'UTF-8';

    private $_title_separator = ' - ';
    
    /** @var tf_layout */
    private $_layout;
    
    /** @var string current template */
    private $_template;

    /** current template path */    
    protected $template_root;
    
    // [templates] from engine.cfg
    protected $templates;
    
    private $_message;
    
    /**
    * Current data
    * 'faculty'
    * 'category'
    * 'post'
    * etc...
    */
    public $current;
    /**
    * Import this from core config
    * @see self::append_import_cfg_vars()
    */
    private $import_cfg = array(
         'template'
       , 'site_url'
       , 'charset'
       , 'in_index'
       , 'rate_level'
       , 'comment_level'
       
       , 'seo_title'
       , 'seo_md'
       , 'seo_mk'
    );
    
    /**
    * All current operation use this
    * @self::set_return(key,value);
    * @self::get_return(key,value);
    */
    public $return;
    
    /**
    * @see self::set_ajax_answer()
    * array(
    *   'type'    => type of answer AJAX_...
    *   'data'    => 
    *       'message' => 'Why we fuck up'
    *       'status'  => false
    */
    private $ajax_answer;
    
    const AJAX_JSON = 'json';
    const AJAX_TEXT = 'text';
    
    /**
    * @public bool
    */
    private $_disable_output;

    /**
     * Construct
     * @param string template root
     * @param object tpl_parser (default: smarty)
     */
    public function __construct($template, $tp) {

        $this->_charset = core::get_instance()->cfg('charset', $this->_charset);
        $this->templates = core::get_instance()->cfg('templates');

        $this->current = new aregistry;
        $this->return = new aregistry;

        $this->set_parser($tp);
        $this->set_template($template);

    }
    
    /**
    * Set parser
    */
    function set_parser($tp) {
        $this->tpl_parser = $tp;
    }
    
    /**
    * Get parser
    */
    function get_parser() {

        if ($this->tpl_parser instanceof Closure) {
            $this->tpl_parser = $this->tpl_parser();
        }

        return $this->tpl_parser;
    }
    
    function disable_output($is) {
        $this->_disable_output = $is;
    }
    
    function set_content_type($type) {
        $this->_content_type = $type;
    }
    
    /**
    * Set page layout
    * default is 'root'
    */
    function set_page_template($name) {
        $this->page_template = $name;
        return $this;
    }

    /**
     * @return string
     */
    function get_page_template() {
        return $this->page_template;
    }
    
    /**
    * Embed layout
    * Disables header and footer for page
    */
    function set_embed_page_template() {
        $this->set_page_template('embed');
    }
    
    /**
    * Set section template
    * Called in core, router must return main_template
    * 
    * @param string without .tpl
    */
    function set_main_template($name) {
        //core::dprint(array('SET MAIN TPL : %s', $name), core::E_DEBUG3);
        $this->set_data('main_template', empty($name) ? false : ($name . loader::DOT_TPL));
        return $this;
    }
    
    /**
    * Set section title
    * 
    * @param string title
    */
    function set_main_title($title) {
        $this->set_data('main_title', $title);
        return $this; 
    }
    
    /**
    * Gets main template 
    */
    function get_main_template() {
        return $this->get_data('main_template');
    }

    /**
     * Page title separator
     * @param mixed $sep ' - '
     * @return renderer
     */
    function set_page_title_separator($sep) {
        $this->_title_separator = $sep;
        return $this;
    }

    /**
     * Set page title
     * Called multiple times
     */
    function set_page_title($title, $append = true) {

        if (!$append) {
            $this->page_title = array();
        }

        if ($title) {
            $this->page_title[] = $title;
        }

        return $this;
    }

    /**
    * Make title
    * @return string title
    */
    function get_page_title() {

        if (empty($this->page_title)) {
            return '';
        }

        $title = $this->page_title;
        settype($title, 'array');

        return htmlspecialchars(count($title) > 1
            ? implode($this->_title_separator, array_reverse($title))
            : $title[0]);
    }
    
    /**
    * Set current data
    */
    function set_current($key, $data) {
        $this->current[$key] = $data;
        return $this; 
    }    

    /**
     * @param $template
     * @return $this
     */
    function set_template($template) {
        $this->_template = $template;
        $this->set_layout($template);
        return $this;
    }
    
    function get_template() {
        return $this->_template;
    }

    /**
     * @param $template
     * @return $this
     */
    function set_layout($template) {
        $core = core::get_instance();
        // template fs-root
        $tpl_root = $core->cfg('site_url') . loader::DIR_TEMPLATES . $template . '/';
        $this->template_url = $tpl_root;
        tpl_loader::set_template($template);
        $this->template_root = $this->get_template_root($template);
        $this->_layout = new tf_layout($this->template_root);
        return $this;
    }
    
    function get_template_root($template = null) {
        if (!isset($template)) return $this->template_root;
        return loader::fix_path(loader::get_public()
            . core::get_instance()->get_cfg_var('site_url') 
            . loader::DIR_TEMPLATES . $template . '/'); 
    }
    
    function get_layout() {
        return $this->_layout;
    }
    
    function get_templates() {
        return $this->templates;
    }   
    
    function get_template_by_id($id = 0) {
        if (empty($id)) return core::get_instance()->get_cfg_var('template');
        return @$this->templates[$id];
    }     
    
    /**
    * Append cfg to import
    */
    function append_import_cfg_vars(array $arr) {
        $this->import_cfg = array_merge($this->import_cfg, $arr);
    }
    
    /**
    * Modules put their information 
    * in render::data
    * Passive mode render
    */        
    public function render_modules() {
        
        $mods = core::modules();
        
        core::dprint('[render modules]');

        foreach ($mods as $pmod) {            
            if ($pmod->is_renderable()) $pmod->render($this);
        }
        
        return true;
    }
    
    /**
    * Output message
    * @return bool|string template
    */
    public function render_message($msg, $data) {
        if (empty($msg)) return false;
        
        $this->set_data('message', $msg)
             ->set_data('message_data', $data);  
    }
    
    /**
    * Set post  
    * @param posts_item 
    */
    function set_post($post) {
        $this->set_data('post',      $post);        
    }    
     
    /**
    * Set posts  
    * @param object {data, pagination} (filter output)
    */
    function set_posts($filt_out) {
        $this->set_filtered_list('posts', $filt_out);
    }

    /**
    * Set post  
    * @param posts_item 
    */
    function set_faculties($facs) {
        $this->set_data('faculties',      $facs);        
    }     
    
    /**
    * Set filtered list to template
    * @param object {data, pagination} (filter output)
    */
    function set_filtered_list($name, $filt_out) {
        $this->set_data($name,          $filt_out->data);        
        $this->set_data('pagination',   $filt_out->pagination);     
		if (isset($filt_out->stored_filters))
		$this->set_data('filters'   ,   $filt_out->stored_filters);     
    }
         
    /**
    * Render block
    * 
    * Tpl placed in template/blocks
    * $Data assigned to {$block.data}
    * 
    * @see module_blocks
    * 
    * @return string html
    */
    public function render_block($props, $data) {

        $props['data'] = $data;
        
        // override template thru block params
        if (isset($props['params']['template'])) {
            $props['template'] = $props['params']['template'];
        }
        
        if (!empty($props['template'])) {
            $this->get_parser()->assign('block', $props);
            $tpl = 'blocks/' . $props['template'] . loader::DOT_TPL;
            return $this->get_parser()->fetch($tpl);
        }
        // direct rendering
        else return $data;
    }
    
    /**
    * Pre out    
    */
    private function output_begin($ext_config = array()) {

        // query current module for info
        $core       = core::selfie();
        $module     = core::modules()->get_router();
        $controller = $module->get_controller();

        // lang constants {$lang._module.value}
        // @deprecated, use smarty i18n modifier
        $this->set_data('lang', $core->get_langwords());
        
		foreach ($this->import_cfg as $key) {
            if (false !== ($test = $core->cfg($key)))
                $cfg[$key] = $test;
        }

        $cfg['template_url'] = $this->template_url;
        $cfg['url']          = $_SERVER['REQUEST_URI'];
        $cfg['title']        = $this->get_page_title();
        $cfg['domain_url']   = $module->get_router()->get_protocol() . $_SERVER['HTTP_HOST'];
        $cfg['static_url']   = $core->get_static_url();
        
        $cfg['debug']        = core::is_debug();
        $cfg['version']      = core::$version;
        $cfg['in_ajax']      = loader::in_ajax();

        $cfg['token']        = $core->auth->token();
        
        if ($rc = $core->get_renderable_config()) {
            $cfg = array_merge($cfg, $rc);
        } 

        // set default title if empty
        if (empty($cfg['title'])) $cfg['title'] = '';
        

        $cfg['module']       = $module->get_name();
        $cfg['section']      = $controller->get_section_name();
        $cfg['action']       = $controller->get_action_name();

        // ridiculous translate stuff @todo rethink
        $cfg['action_title'] = /*i18n*/ $controller->get_title();
        
        // mix configs
        if (!empty($ext_config)) {
            $cfg = array_merge($cfg, $ext_config);
        }
        
        if (core::in_editor()) {
            $cfg['editor'] = $core->cfg('editor', array());
        }

        // user
        $this->render_user();

        // render modules
        $this->render_modules();

        // this go to template
        $this->set_data('req',     core::get_params()->as_array())
             ->set_data('return',  $this->return)
             ->set_data('current', $this->current)
             ->set_data('config',  $cfg);
        ;

    }
    
    private $_buffer;
    
    function get_buffer() {
        return $this->_buffer;
    }
    
    /**
    * Post out
    * Finish output
    */
    private function output_end($tpl, $return = false) {

        $this->_buffer = '';
        
        if ($this->_is_null_template()) return;
        
        /* display if template given
           or return if $return passed 
           
           skip if template is empty (ajax)         
        */
        
        if (!empty($tpl)) {

            $tpl .= loader::DOT_TPL;
            
            // assign data        
            $this->get_parser()->assign($this->get_data());

            core::dprint('[RENDER] using ' . $tpl . ' with ' . $this->get_main_template() . ' [' . $this->template_url . ']');        
    
        
            // in ajax dies silently, if no template found
            try {
                $this->_buffer = $this->get_parser()->fetch($tpl);
            }
            //@fixme not portable parser
            catch (SmartyException $e) {
                $this->_buffer = '<code>ParserException: ' . $e->getMessage()
                    . (core::is_debug() ? nl2br($e->getTraceAsString()) : '')
                    . '</code>';
            }

            // make some editor magic
            if (core::in_editor()) {
                core::lib('editor')->on_output_before($this->_buffer);
            }

            core::event('core_after_output', $this->_buffer);
             
            // fix spaces (30260 -> 27611 time 0.0017)            
            // $html = preg_replace('#>[\s]{2,}<#smU', '><', $html);
            // $html = str_replace(array("\n", "\r", '  '), '', $html);
            // $this->tpl_parser->display($tpl);

            $this->_buffer = trim($this->_buffer);
            
            if ($return) {
                return  $this->_buffer;
            }
            else {
                echo $this->_buffer;
            }
        }
    }
     
    /**
    * @return self
    */
    function set_null_template() {
        $this->set_main_template(false);
        $this->_null_template = true;
        return $this;
    } 
    
    private $_null_template = false;
    function _is_null_template() {
        return $this->_null_template;
    }

    /**
    * Call this before @see self::set_ajax_message() @see self::set_ajax_result()
    * Set ajax output type and data ususaly called in controller
    * 
    * use controller::set_null_template() to return raw ajax results (without template)
    * 
    * AJAXResponse: {message,status,..,data[var],...data=>парсенный шаблон, если указан}
    * 
    * @param string 
    *   self::AJAX_JSON #default
    *   self::AJAX_TEXT
    * @param array data submited to ajax
    * @return self
    */
    public function set_ajax_answer($data = null, $type = self::AJAX_JSON) {
        if (!$data && $type == self::AJAX_JSON) $data = array();
        $this->ajax_answer = array('type' => $type, 'data' => $data);
        return $this;
    } 
    
    /** ajax defaults */
    private function _set_ajax_answer() {
        if (!is_array($this->ajax_answer) || !isset($this->ajax_answer['data']))
        $this->ajax_answer = array('type' => self::AJAX_JSON, 'data' => array());
        return $this;
    }  
    
    /** @return self */ function set_ajax_message($msg)   { $this->_set_ajax_answer(); $this->ajax_answer['data']['message']   = $msg; return $this; }
    /** @return self */ function set_ajax_result($res)    { $this->_set_ajax_answer(); $this->ajax_answer['data']['status']    = $res; return $this; }
    /** @return self */ function set_ajax_type($res)      { $this->_set_ajax_answer(); $this->ajax_answer['type']              = $res; return $this; }
    /** @return self */ function set_ajax_redirect($res)  { $this->_set_ajax_answer(); $this->ajax_answer['data']['url']       = $res; return $this; }
    /** @return self */ function set_ajax_validator($v)   { $this->_set_ajax_answer(); $this->ajax_answer['data']['validator'] = $v; return $this; }
    /** @return self */ function set_ajax_data($v)        { $this->_set_ajax_answer(); $this->ajax_answer['data']['data']      = $v; return $this; }
  

    /**
    * Render current user
    */
    public function render_user() {
        $this->set_data('user', core::lib('auth')->render());  
    }  
    
    /**
    * @desc 
    */
    public function switch_simple_output($f = true) {
        $this->simple_output = $f;
    }
    
    /**
     * Set response header
     * @param null $content_type
     * @param null $charset
     */
    function content_type_header($content_type = null, $charset = null) {
        $content_type = isset($content_type) ? $content_type : $this->_content_type;
        $charset = isset($charset) ? $charset : $this->_charset;

        if (!empty($content_type) && !headers_sent())
            header('Content-Type: ' . $content_type . '; charset=' . $charset);
    }
    
    /**
    * Flush ajax call thru renderer
    * Script dies after this method!
    * 
    * call ->set_ajax_answer(data, type))
    * $type
    * 
    * @param mixed $tpl
    *   false - disable template
    *   null - use default assigned
    *   string - assign new one
    */
    function ajax_flush($tpl = false) {
        if (isset($tpl)) $this->set_main_template($tpl);
        $this->output_ajax();
        core::get_instance()->halt();
    }
         
    /**
    * Triggers in ajax mode call
    * Template assign through: @see get_main_template()
    * called from @see core::shutdown
    */
    public function output_ajax($is_return = false) {

        /*
        //disabled in core
        if ($console = core::lib('console')) {
            $console->disable();
        }
        */

        $this->output_begin(
            array('in_ajax' => true)
        );

        // output_ajax conflict in editor
        // @todo test this

        if (loader::in_ajax() === 'json') {
            $this->set_ajax_type('json');
        }

        if (core::in_editor() /*&& $tpl === null*/) {
            $this->output_editor();
        }

        $tpl = $this->get_main_template();

        if ($tpl) {
            $tpl = substr($tpl, 0, -4); // output_end will append .TPL
        }

        if (!$is_return && self::AJAX_JSON == @$this->ajax_answer['type']) {
            $this->content_type_header('application/json', 'utf-8');
        }

        /**
        * JSON answer
        */                   
        if (!empty($tpl) && self::AJAX_JSON == @$this->ajax_answer['type']) {
                              
            // parse template, if any                   
            $buffer = $this->output_end($tpl, true);
            
            // output ajax json data
            $return = is_array($this->ajax_answer['data'])
                ? $this->ajax_answer['data']
                : array();                    
            
            // if rendered with template
            if (isset($buffer)) {
                $return['data'] = $buffer;
            }

            // send message
            if (empty($tpl) && !empty($this->_message['message'])) {
                $return['message'] = $this->_message['message'];
            }            
            
            echo json_encode($return);
        }
        elseif (empty($tpl)) {

            // allow prepared json
            echo self::AJAX_JSON == $this->ajax_answer['type']
                ? (is_string($this->ajax_answer['data']) ? $this->ajax_answer['data'] : functions::json_encode($this->ajax_answer['data']))
                : @$this->ajax_answer['data']['data'];

        }
        else {
            // plain output
            $this->output_end($tpl);
        }
    }
        
    /**
    * Editor out    
    */
    public function output_editor() {

        /*
            boolean false
            string 'index' (length=5)
            null

            var_dump($this->get_main_template(), $this->get_page_template());
            dd($template, __METHOD__);
        */

        if (($m = core::lib('request')->get_ident()->m) && !empty($m)) {
            // root template relative                      
            $this->set_data('modtpl_prefix', '../../modules/' . $m . '/editor/templates/');
            $this->data['config']['base_url']   = core::module($m)->get_editor_base_url();
            $this->data['config']['editor_url'] = core::get_instance()->cfg('site_url') . loader::DIR_EDITOR;
        }

        if (core::get_params('embed')) {
            $this->set_page_template('embed');

            // allow embed in ajax

            if (loader::in_ajax() && !$this->get_main_template()) {
                $this->set_main_template('embed');
            }
        }

        /*
        else {
            $layout = core::get_instance()->cfg('editor.layout');
            $template = $layout ? "layout/{$layout}/" : '';
            $template .= 'root';
            //$template .= loader::DOT_TPL;
        }
        */
    }
    
    /**
    * @param array|string
    * @param array array('is_error')
    */
    function set_message($msg, $data = null) {
        
        if (is_array($msg)) {}
        
        $this->_message = array(
              'message' => $msg
            , 'data'    => $data
        );
    }
         
    /**
    * Final output     
    * called from @see core::shutdown
    */        
    public function output() {

        if (loader::in_ajax()) {
            return $this->output_ajax();
        }
    
        /** give up if in crontab */
        if (loader::in_shell()) return false;
             
        $core = core::get_instance();
 
		core::dprint('renderer::ouput', core::E_DEBUG0); 
    
        $this->content_type_header();
        
        // disabled
        if ($this->_disable_output) return false;
        
        $cfg_data = array();
        
        // initial
        $this->output_begin();
        
        if (core::in_editor()) {
            $this->output_editor();
        }

        $tpl = $this->page_template;

        // check message
        
        if ($msg = $core->get_message()) {
            $this->render_message($msg, $core->get_message_data());            
        } elseif (!empty($this->_message)) {                                       
            $this->render_message($this->_message['message'], @$this->_message['data']); 
        }             
        
                          
        // no template
        if (empty($tpl)) {
            core::dprint('render with empty page template assigned');
            //throw new renderer_exception('render with empty page template assigned');
        }

        /*
            Throwing exception here 
            Will result in 
            Fatal error: Exception thrown without a stack frame in Unknown on line 0
        */
        if (false == $this->get_main_template()) {
            core::dprint('render with empty main template assigned');
            // throw new renderer_exception('render with empty main template assigned');
        }
        
        $this->output_end($tpl);

        
    }       
    
    /**
    * Set return
    * root level namespace [return]                                               
    */
    function set_return($key, $value = false) {
        if (is_array($key)) $this->return = $key;
        else $this->return[$key] = $value;
        return $this; 
    }
    
    /**
    * Get return
    * root level namespace [return]                                               
    */
    function get_return($key = false) {
        return ($key) ? $this->return[$key] : $this->return;
    }
}                                                     