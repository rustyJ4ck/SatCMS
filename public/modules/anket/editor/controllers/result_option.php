<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: result_option.php,v 1.1.2.1 2012/09/14 18:38:39 j4ck Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');


  
class anket_result_option_controller extends editor_controller {
    
    protected $collection_config = array('with_module_prefix' => 1);
    
    protected $title = 'Типы результатов';
    
    protected $_where = 'pid = %d';
      


    
    private $_anket;
        
    function construct_before() {
        

        
        $this->_where = sprintf($this->_where, $this->params->pid);
        
        if (!$this->params->pid) throw new controller_exception('Empty pid');
    }
    
    function action_before() {
        $this->_anket = $this->context->get_form_handle()->load_only_id($this->params->pid);
        $this->renderer->set_current('anket_form',
            $this->_anket->render()
        );
    }
    
}

