<?php

/**
 * Editor APP
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: editor.php,v 1.2.6.5 2012/12/13 13:30:07 Vova Exp $
 */

 class tf_editor {
     
    private $_disable_sef = false;
     
    /** @var tf_request */
    private $request; 
    /** @var core_module */
    private $module;
    /** @var users_item */
    private $user;
    /** @var  tf_renderer */
    private $renderer;
    
    private $_actions;
    private $_title;
    
    protected $_is_submitted = false;
     
    function __construct() {
        $this->request = core::lib('request');
        $this->user = core::lib('auth')->get_user();
        $this->renderer = core::lib('renderer');
    } 
    
    /**
    * Assign module menu ($m_menu)
    * 
    * array() = [key]=>{'title'=>, 'url'=>}
    * 
    * @param array
    * @deprecated
    */      
              
    function assign_module_menu($m) {
        /*
        // this done in on_after_output
        foreach ($m as &$v) {
            if (isset($v['url'])) $v['url'] = $this->make_url($v['url'], 1);
        }
        */
        $this->_actions = $m;
        core::lib('tpl_parser')->assign('m_menu', $m);
    }
    
    function set_title($title) {
        $this->_title = $title;
    }
    
    
    private $_exception = false;
    
    function on_exception($message, $e = null) {

        if ($this->_exception) return; // triggered twice?
        $this->_exception = true;

        if (!$e) {
            $e = new Exception($message);
        }

        $this->renderer->set_data('exception', $e);

        if (loader::in_ajax()) {
            $this->renderer
                ->set_ajax_message($e->getMessage())
                ->set_ajax_result(false)
                ->ajax_flush()
                ;
        } else {
            $this->renderer->set_template('error'); // @todo вникуда: output_editor override this
        }
    }
    
    /**
    * Called from module::on_editor
    * @throws acl_exception
    */
    function on_editor($mod) {
        
        $this->module = $mod;
        
        $section = core::get_params('c');
        $id = (int)core::get_params('id');
        
        // module checks goes on top, so skip if section empty
        if (empty($section)) return;       
                
        if (!core::module('users')->with_acls()) return;
        
        // override section acl / id
        if (isset($this->_actions[$section]['acl_id'])) $id = core::get_params($this->_actions[$section]['acl_id']);
        if (isset($this->_actions[$section]['acl'])) $section = $this->_actions[$section]['acl'];
        
        $op = 'read';

        // @todo check this
        $is_submitted = (bool)$this->request->get_post('is_submitted', 0);
        
        $_op = core::get_params('op');
        
        if ($_op == 'edit') $op = 'update';                          
        if ($_op == 'drop') $op = 'delete';
        if (empty($id) && $is_submitted)  $op = 'create';
        if (!empty($id) && $is_submitted) $op = 'update';
                                 
                                 
        if (is_callable(array($mod, 'editor_check_acls'))) { 
            $result = call_user_func(array($mod, 'editor_check_acls'),
                array(
                      'section'       => &$section
                    , 'section_id'    => &$id
                    , 'action'        => &$op
                ));
              
             // WARN! true skips checks
             if ($result === true) {
                 return;
             }          
        }
        
        $this->check_acls($section, $id, $op);
        
    }
    
    /**
    * Parse path
    * Set ident vars for editor controller
    * /module/controller/pname/pvalue/.../
    * 
    * Do not ovverride identvars with (pid,.. etc) URL, respect post data
    */
    function dispatch($path, request_params $params) {

        if (empty($path)) return;

        $path = explode('/', $path);
        
        $module     = array_shift($path);
        
        if (isset($module)) $params->m = $module;
        
        $controller = array_shift($path);
        
        if (isset($controller)) $params->c = $controller;

        $post = core::lib('request')->post();
        
        if (!empty($path)) {
            while(1) {
                $pname  = array_shift($path);
                $pvalue = array_shift($path);
                if (isset($pname) && isset($pvalue) && !isset($post[$pname])) {
                    
                    $params->set($pname, $pvalue);
                }
                else break;
            }
        }      
    }

     /**
      * Angular url
      */
     function make_ng_url($url, $sef) {

         $url = $this->make_url($url, $sef);
         $url = str_replace('editor/', 'editor/redirect/', $url);
         return $url;
     }

    /**
    * Normalize url
    * 
    * @param string $url baseUrl
    * @param mixed $sef make-sluggable
    */
    function make_url($url, $sef = false) {

        // core::dprint(__METHOD__ . ' : ' . $url);

        $DIR_EDITOR = '/' . loader::DIR_EDITOR; // /editor/

        if (!$this->_disable_sef && $sef && strpos($url, 'm=') !== false) {
            $purl = parse_url($url);

            // parsed query string []
            $pa = array();

            $url = '';
            $pnewurl = array();

            // /path?query
            
            if (!empty($purl['query'])) {

                parse_str($purl['query'], $pa);

                if (isset($pa['m'])) {
                    $pnewurl []= $pa['m'];
                    unset($pa['m']);
                }
                
                if (isset($pa['c'])) {
                    $pnewurl []= $pa['c'];
                    unset($pa['c']);
                }
                
                if (!empty($pa)) {
                    foreach ($pa as $k => $v) {
                        if (!empty($v)) {
                            $pnewurl []= $k;
                            $pnewurl []= $v;
                        }
                    }
                }
                
                $url = join('/', $pnewurl);
            }
            
            $url .= '/';
            
        }
        
        $url = $DIR_EDITOR . $url;
        
        return $url;
    }
    
    /**
    * Fix old style urls
    * 
    * @param mixed $buffer
    */
    function on_output_before(&$buffer) {
        /*
        self:: Fatal error: Cannot call method self::output_replace_callback() or method does not exist in /modules/core/libs/editor.php on line 214
        */
        $buffer = preg_replace_callback(
            '@(?P<f>[\"|\'])(?P<url>(index\.php)?\?m=.+)(?P<l>[\"|\'])@U'
            , 'tf_editor::output_replace_callback'
            , $buffer
        ) ;
    }
    
    private static function output_replace_callback($m) {
        return @$m['f'] . core::lib('editor')->make_url($m['url'], 1) . @$m['l'];
    }
      
    /**
    * Check acls
    * 
    * @param mixed $section
    * @param mixed $id
    * @param mixed $op
    * @param mixed $is_submitted
    */
    function check_acls($section, $id, $op) {
        
        if (!$this->user->is_allow($section, $id, $op)) {
            core::dprint(array("Access denied %s %s %s", $section, $id, $op));
            throw new acl_exception(sprintf("Access denied %s %s %s", $section, $id, $op));
        }
        
    }
 }