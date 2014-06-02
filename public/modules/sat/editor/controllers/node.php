<?php

/**
 * Nodes Controller 
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: sat_node.php,v 1.1.2.6.2.7 2012/12/20 06:47:30 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

use SatCMS\Modules\Sat\Editor\Controllers\SatController;

/**
 * Class sat_node_controller
 * @property tf_sat context
 */
class sat_node_controller extends SatController {

    private $_site;
    
    protected $title = 'Контент';
    
    protected $_limit = 25;    
    protected $_where = 'site_id = %d AND pid = %d';
    
    /** @var sat_node_collection */
    protected $collection;
    
    //protected $_where = 'site_id = %d';   
    
    function construct_after() {

        $this->params->pid = 0 + $this->request->postget('pid', $this->params->pid);
        
        if (!$this->site) {
            throw new editor_exception('Сайт не выбран');
            return;
        }
        
        // подшаблоны для макета сайта (current_site hack)  
        $psat_templates = $this->renderer->get_layout()
                ->init($this->site->template 
                    ?   $this->renderer->get_template_root(
                            $this->renderer->get_template_by_id($this->site->template))
                    : false)
                ->get_templates();

        $this->renderer->set_data(
            'subtemplates'
            , $psat_templates 
        );
        
        $this->_where = sprintf($this->_where
            , (int)$this->get_site_id()
            , (int)$this->params->pid);
            
            
        $this->base_url = $this->base_url . '&pid=' . $this->params->pid;
        
    }
    
    function action_clear_static() {
        $node = $this->_load_id();
        if ($node) {
            $node->clear_static();
            $this->ajax_answer(true, 'Статичный кэш сброшен', $node->id);
        }                           
    }           
    
    /**
    * @param mixed 
    *   false   - disable sync
    *   number  - only pid
    *   null    - full
    */
    protected function _update_tree($pid = null) {

        $this->context->update_tree(
            $this->get_site_id()
            , false //disable full sync
        );    
        
        // sync parent
        if (!empty($pid)) $this->collection->sync_children_count($this->get_site_id(), $pid);
    }
    
    function action_change_field_after() {
        $this->_update_tree(false);    
    }
    
    function action_active_after() {
        $this->_update_tree(false);
    }
    
    function action_flip_after() {
        $this->_update_tree(false);
    }
    
    function action_drop_all_after(/*$ids*/) {
        $this->_update_tree($this->params->pid);
    }
    
    function action_drop_after() {
        $this->_update_tree($this->params->pid); 
    }
    
    function action_modify_before(&$data, $item) {        
        $data['site_id']    = $this->params->id ? $item->site_id : $this->get_site_id();          
        $data['modify_uid'] = $data['owner_uid'] = core::lib('auth')->get_user()->id; 
    }
    
    function action_modify_after($id) {

        // optimize?
        $this->_update_tree($this->params->id ? false : $this->params->pid);  
        $this->collection->clear_static();

        /*
        if (!$this->params->id && !empty($id) && !$this->request->post('save_continue')) {        
           // new one, redirect
           // $this->get_router()->make_url
           $url = $this->make_url('op=edit&id=' . $id);
           functions::redirect($url);
           core::get_instance()->halt();        
        }
        */
    }        
    
    function action_new_before() {
        $this->collection->load_secondary();
    }
    
    function action_edit_before($item) {
        $this->collection->load_secondary();                
    }

    function action_vis() {
        $this->set_template('list.vis');
    }
     
    function action_before() {
        // render tree    
        $tree = $this->context->get_current_site_tree();
        $this->renderer->set_data('tree', $tree);
        

        $nav_id = ($this->params->op == 'edit') ? $this->params->id : $this->params->pid;

        // nav-chain
        if ($nav_id) {

           $this->context->get_node_handle()->get_parents($nav_id)
             ->prepend($this->context->get_root_node())
             ->append($this->context->get_node($nav_id))
             ->set_tpl_table('parent_chain')
             ->render2edt();     
        }
        
     }

    function _toggle_flag($flag) {
        $this->collection->toggle_flag($flag, $this->params->id, ('true' == functions::request_var('to', 'false')));
        if ($this->in_ajax()) { $this->_ajax_answer(true, i18n::T('Status changed')); }
        $this->_update_tree($this->params->pid);
    }

    function action_toggle_system() {
        $this->_toggle_flag('b_system');
    }

    function action_toggle_featured() {
        $this->_toggle_flag('b_featured');
    }
    
}
