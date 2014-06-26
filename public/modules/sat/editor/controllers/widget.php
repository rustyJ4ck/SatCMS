<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: widget.php,v 1.1.2.1 2013/01/30 06:53:30 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

use SatCMS\Sat\Editor\Controllers\SatController;
  
class sat_widget_controller extends SatController {
    
    protected $title = 'Виджеты';
     
    protected $_where = 'pid = %d'; 
    
    protected $cmd_pid;

    function construct_after() {

        $this->cmd_pid = $this->params->pid;                     

        $this->_where = sprintf($this->_where
            , (int)$this->params->pid
        );
            
        $this->base_url = $this->base_url . '&pid=' . $this->params->pid;
    }
    
    function action_before() {
        if (!empty($this->cmd_pid)) {
            $this->response->parent = core::module('sat')->get_widget_group_handle()->load_only_id($this->cmd_pid)->render();
        }
    }
    
    function action_modify_before() {
        if (empty($this->params->id)) {
            $this->postdata['pid'] = $this->cmd_pid; 
        }
        else {
            $this->postdata['pid'] = $this->_load_id()->pid;
        }
    }

}

