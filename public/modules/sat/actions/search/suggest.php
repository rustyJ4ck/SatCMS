<?php
      
/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: suggest.php,v 1.1.2.1 2012/10/18 06:59:59 Vova Exp $
 */

/**
* Suggest data
*/
class sat_search_suggest_action extends controller_action {
    
    
    function run() {
        
        $q = trim(urldecode($this->request->get('q')));
        
        $q = core::lib('db')->escape($q);
        
        if (strings::strlen($q) < 2) {
            $this->renderer
                ->set_ajax_result(false)
                ->set_ajax_message('Короткое сообщение')
                ->ajax_flush(false);
            return;
        }
        
        $pmod = $this->_controller->get_context();
        $ph = $pmod->get_search_handle()
                   ->set_working_fields('keyword')
                   ->set_limit(10);
         
        if (!empty($q)) {
            $ph->set_where("keyword like '%{$q}%' AND c_count > 0");
        }
        
        $sugg = $ph->load()->render();
                  
        core::get_instance()->ajax_answer($sugg);        
        
    }
}