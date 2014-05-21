<?php

/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: view.php,v 1.1.2.1 2012/10/25 09:52:43 Vova Exp $
 */


/**
* Вопрос-ответ (смотреть)
*/
class contacts_qa_view_action extends controller_action {          

    function run() {               
        $id         = $this->get_param('id');
       
        if (empty($id)) {
            throw new controller_exception('Bad id');
        }
        
        $pitem = $this->_controller->get_context()->get_qa_question_handle()
            ->append_where_vf('url', $id)
            ->set_limit(1)
            ->load()
            ->load_secondary()
            ->get_item();
        
        if (!$pitem) {
            throw new controller_exception('Question not found');
        }
          
        $this->renderer->set_return('item', $pitem->render());  
                                  
        $this->_controller->set_title_params($pitem->title);
        
    }           
    
}
