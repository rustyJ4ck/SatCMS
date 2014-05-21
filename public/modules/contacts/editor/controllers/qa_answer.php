<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: qa_answer.php,v 1.1.2.1 2012/10/25 09:52:44 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');


  
class contacts_qa_answer_controller extends editor_controller {
    
    protected $collection_config = array('with_module_prefix' => 1);
    
    protected $title = 'Ответы';
    
    protected $_where = 'pid = %d';
      


    
    private $_parent;
        
    function construct_before() {
        

        
        $this->_where = sprintf($this->_where, $this->params->pid);
        
        if (!$this->params->pid) throw new controller_exception('Empty pid');
    }
    
    function action_before() {
        $this->_parent = $this->context->get_qa_question_handle()->load_only_id($this->params->pid);
        $this->renderer->set_current('parent',
            $this->_parent->render()
        );
        
    }

    function action_after($op) {
        if ($op == 'modify' || $op == 'drop') {
            $this->_parent->sync_count();            
        }
    }     
}

