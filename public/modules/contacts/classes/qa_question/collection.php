<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.2 2013/01/30 06:53:28 Vova Exp $
 */  
  
/** collection */
class contacts_qa_question_collection extends abs_collection {
    /**
    * Approve switch
    */
    function toggle_active($id, $value) {  
        $this->update_item_fields($id, 
            array('active' => $value)
        );                      
    }  
}

/** item */
class contacts_qa_question_item extends abs_collection_item {
    
    /** @var contacts_qa_answer_collection */
    protected $_answers;
    
    function make_urls() {
        $this->append_urls('self', '/contacts/qa/view/' . $this->url . '/');
    }
    
    function get_answers() {
        if (!isset($this->_answers)) {
            $this->_answers = core::module('contacts')->get_qa_answer_handle()
                ->set_where('pid = %d', $this->id)
                ->set_order('date')
                ->load();
        }
        return $this->_answers;
    }
    
    function render_after(&$data) {
        if (!empty($this->_answers)) {
            $data['answers'] = $this->_answers->render();
        }
    }
    
    function load_secondary($options = NULL) {
        $this->get_answers();
        return $this;
    }

    /** sync answers count */    
    function sync_count() {
        $this->c_count = core::module('contacts')->get_qa_answer_handle()->set_where('pid = %d', $this->id)->count_sql();
        $this->update_fields('c_count'); 
    }
    
    function remove_before() {
        $this->get_answers()->remove_all();
    }
    
}