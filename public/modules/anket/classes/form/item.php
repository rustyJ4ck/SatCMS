<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.1.2.3 2012/09/16 18:00:41 j4ck Exp $
 */
 
class anket_form_item extends abs_collection_item {
    
    /** @var anket_question_collection  */
    protected $_questions;
    /** @var anket_result_option_collection  */
    protected $_result_options;
    
    /** @return anket_question_collection */
    function get_questions() {
        if (!isset($this->_questions)) {
            $this->_questions = core::module('anket')->get_question_handle()->set_where('pid = %d', $this->id)->load();
        }
        return $this->_questions;
    }
    
    /** @return anket_result_option_collection */
    function get_result_options() {
        if (!isset($this->_result_options)) {
            $this->_result_options = core::module('anket')->get_result_option_handle()->set_where('pid = %d', $this->id)->load();
        }
        return $this->_result_options;
    }    
    
    function remove_after() {  
        $this->get_questions()->remove_all();
        $this->get_result_options()->remove_all();
    }
    
    function load_secondary() {
        $this->get_questions()->load_secondary();
        $this->get_result_options();        
        return $this;
    }
    
    function render_after(&$data) {
        if (isset($this->_questions)) $data['questions'] = $this->_questions->render();
        if (isset($this->_result_options)) $data['result_options'] = $this->_result_options->render();
    }
    
    /**
    * @param mixed $data
    * @return 
    */
    function process_form($data) {            
        // create result
        // attach result-option to result
        // return result
    }
}