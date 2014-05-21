<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: result.php,v 1.1.2.1.2.5 2012/09/20 08:14:34 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');


  
class anket_result_controller extends editor_controller {
    
    protected $collection_config = array('with_module_prefix' => 1);
    
    protected $title = 'Результаты';
    
    protected $_where = 'pid = %d';
      


    
    private $_anket;
    
    protected $_limit = 20;
        
    function construct_after() {
        

        
        $this->_where = sprintf($this->_where, $this->params->pid);
        
        // allow view without PID
        if (!$this->params->pid && $this->params->id) {
            $this->params->pid = $this->_load_id($this->params->id)->pid;
        }
        
        if (!$this->params->pid) throw new controller_exception('Empty pid');
        
        $this->base_url .= sprintf('&pid=%d', $this->params->pid);
    }
    
    function action_before() {
        $this->_anket = $this->context->get_form_handle()->load_only_id($this->params->pid);
        $this->renderer->set_current('anket_form',
            $this->_anket->render()
        );
    }
    
    function action_edit_before($item) {
        $item->set_data('result_option',
         ($res = $item->get_result_option()) ? $res->render() : false
        );
    }
}

