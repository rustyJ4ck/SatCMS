<?php

/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: list.php,v 1.1.2.1 2012/10/25 09:52:43 Vova Exp $
 */

/**
* Список вопросов-ответов
*/
class contacts_qa_list_action extends controller_action {
    
    private $_per_page = 5;
    
    function run() {
        
        $site_id = core::module('sat')->get_current_site_id();
        $page = ($pf = $this->_controller->get_router()->get_filter('pagination')) ? $pf->get_start() : 0;
           
        /** @var \tf\core\collection_filter*/ 
        $f = $this->_controller->get_context()->get_qa_question_handle()
            ->get_filter('/contacts/qa/')
                ->set_config(array(
                  'where_sql'        => "site_id = {$site_id} AND active"                  
            ))
            ->set_per_page($this->_per_page)
            ->set_pagination($page);

        $this->renderer->set_return(
            'posts', $f->apply()->as_array()
        );
             
    }
    
}