<?php

/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: answer_new.php,v 1.1.2.1 2012/10/25 09:52:43 Vova Exp $
 */

/**
* Новый вопрос-ответ
*/
class contacts_qa_answer_new_action extends controller_action {    

    
    function run() {
        
        $pid = (int)$this->request->post('pid');
            
        if ($this->request->post('form_submit') == 'yes') {
            
            $_post = $this->request->get_post();
            
            $parent  = $this->_controller->get_context()->get_qa_question_handle()->load_only_id($pid);                                     
            
            if (!$parent) {
                throw new controller_exception('Bad parent');
            }
            
            $pres  = $this->_controller->get_context()->get_qa_answer_handle();                                     
            
            /** @var tf_auth */
            $auth = core::lib('auth');
            
            $_post['session_id'] = $auth->get_current_session()->get_id();
            
            $aid = $pres->create($_post);
            
            $parent->sync_count();
            
            $this->renderer
                ->set_ajax_message('Обработка запроса')
                ->set_ajax_result((bool)$aid)
                ->set_ajax_data($aid ? $pres->get_last_item()->render() : false)
                //->set_ajax_redirect('/contacts/form/complete/')
                ->ajax_flush(false);
    
        }
    
    }
    
    
    
}
