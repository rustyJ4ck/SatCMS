<?php

/**
 * @package    satcms
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: module.php,v 1.1.2.2 2012/10/25 09:52:42 Vova Exp $
 */  
     
class tf_contacts extends core_module {

     /** @return contacts_form_collection */       function get_form_handle()                { return $this->class_register('form', array('with_module_prefix' => true)); }    
     /** @return contacts_qa_question_collection */       function get_qa_question_handle()  { return $this->class_register('qa_question', array('with_module_prefix' => true)); }    
     /** @return contacts_qa_answer_collection */       function get_qa_answer_handle()       { return $this->class_register('qa_answer', array('with_module_prefix' => true)); }    
    
}