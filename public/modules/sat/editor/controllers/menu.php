<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

use SatCMS\Modules\Sat\Editor\Controllers\SatController;

class sat_menu_controller extends SatController {
    
    protected $title = 'Меню';
     
    protected $_where = 'site_id = %d AND pid = %d'; 
    
    protected $cmd_pid;

    function construct_after() {
        $this->cmd_pid = $this->params->pid;                     

        $this->_where = sprintf($this->_where
            , (int)$this->get_site_id()
            , (int)$this->params->pid
        );
            
        $this->base_url = $this->base_url . '&pid=' . $this->params->pid;         
              
    }
    
    function action_before() {
        if (!empty($this->cmd_pid)) {
            $this->renderer->set_current('menu',
                $this->_load_id($this->cmd_pid)->render()
            );            
        }
        
        $tree = $this->context->get_current_site_tree();          
        $this->renderer->set_current('tree', $tree);        
    }
    
    function action_modify_before() {
        if (empty($this->params->id)) {
            $this->postdata['pid'] = $this->cmd_pid; 
        }
        else {
            $this->postdata['pid'] = $this->_load_id()->pid;
        }
    }
    
    // old stuff?
    function action_edit_after($_item) {
        
        if ($this->params->do == 'change_title') {
            $_item->title = functions::request_var('title');
            $_item->update_fields('title');
            if ($this->in_ajax()) $this->ajax_answer(true);
        }
        
        if ($this->params->do == 'change_url') {
            $_item->title = functions::request_var('url');
            $_item->update_fields('url');
            if ($this->in_ajax()) $this->ajax_answer(true);
        }
        
    }

}

