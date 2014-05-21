<?php

/**
 * @package    satcms
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: module.php,v 1.1.2.1.2.1 2012/09/14 18:38:43 j4ck Exp $
 */  
     
class tf_anket extends core_module {

     /** @return anket_form_collection */       function get_form_handle()         { return $this->class_register('form', array('with_module_prefix' => true)); }    
     /** @return anket_question_collection */   function get_question_handle()     { return $this->class_register('question', array('with_module_prefix' => true)); }    
     /** @return anket_answer_collection */     function get_answer_handle()       { return $this->class_register('answer', array('with_module_prefix' => true)); }    
     /** @return anket_result_collection */     function get_result_handle()       { return $this->class_register('result', array('with_module_prefix' => true)); }    
     /** @return anket_result_option_collection */     function get_result_option_handle()       { return $this->class_register('result_option', array('with_module_prefix' => true)); }    
    
}