<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.1.2.1 2012/08/30 07:21:01 Vova Exp $
 */
 
class anket_question_item extends abs_collection_item {
    
    protected $_answers;
    
    /** @return anket_answer_collection */
    function get_answers() {
        if (!isset($this->_answers)) {
            $this->_answers = core::module('anket')->get_answer_handle()->set_where('pid = %d', $this->id)->load();
        }
        return $this->_answers;
    }
    
    function remove_after() {  
        $this->get_answers()->remove_all();
    }
    
    function load_secondary() {
        $this->get_answers();
        return $this;
    } 
    
    function render_after(&$data) {
        if (isset($this->_answers)) $data['answers'] = $this->_answers->render();
    }       
}