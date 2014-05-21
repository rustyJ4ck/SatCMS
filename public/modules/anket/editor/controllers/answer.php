<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: answer.php,v 1.1.2.1.2.4 2012/09/14 18:38:38 j4ck Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');


  
class anket_answer_controller extends editor_controller {
    
    protected $collection_config = array('with_module_prefix' => 1);
    
    protected $title = 'Ответы';
    
    protected $_where = 'pid = %d';
      


    
    private $_anket;
    private $_question;
        
    function construct_before() {
        

        
        $this->_where = sprintf($this->_where, $this->params->pid);
        
        if (!$this->params->pid) throw new controller_exception('Empty pid');
    }
    
    function action_before() {
        $this->_question = $this->context->get_question_handle()->load_only_id($this->params->pid);
        $this->renderer->set_current('anket_question',
            $this->_question->render()
        );
        
        $this->_anket = $this->context->get_form_handle()->load_only_id($this->_question->pid);
        $this->renderer->set_current('anket_form',
            $this->_anket->render()
        );        
    }
    

    function action_toggle_valid() {
        $this->collection->toggle_valid($this->params->id, ('true' == functions::request_var('to', 'false')));
        if ($this->in_ajax()) { $this->_ajax_answer(true, i18n::T('Status changed')); }            
    }       
}

