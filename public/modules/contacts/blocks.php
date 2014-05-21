<?php

/**
 * blocks
 * 
 * @package    content
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: blocks.php,v 1.1.2.1 2012/10/25 09:52:42 Vova Exp $
 */
  
class contacts_blocks extends module_blocks {
    
    /**
    * predefined blocks
    */
    protected $_blocks = array(
          'qa'      => array('template'  => 'contacts/qa',    'title' => 'Вопрос-ответ')
    );                          
     
    /**
    * Similar posts
    * @param array (site_id, pid, count)
    */
    function qa($params = null) {        
        // 'pid' => int 558
        $site_id = core::module('sat')->get_current_site_id();
        $count = isset($params->count) ? $params->count : 5;
        $ctx = $this->get_context();
        
        $posts = $ctx->get_qa_question_handle();
        $posts
            ->set_limit($count)
            ->set_where('site_id = %d AND active', $site_id)
            ->load();
        
        return ($posts ? $posts->render() : false);
    }     
}       
