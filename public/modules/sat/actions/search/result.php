<?php
      
/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: result.php,v 1.1.2.3 2012/11/13 13:42:17 Vova Exp $
 */


/**
* Suggest data
*/
class sat_search_result_action extends controller_action {
    
    private $_per_page = 10;
    
    function run() {  
        
        $id   = (int)$this->get_param('id');      
        $page = (int)$this->get_param('page'); 
        
        // @todo move pagination to filters?
        if (!$page) {
            $page = ($pf = $this->_controller->get_router()->get_filter('pagination')) ? $pf->get_start() : 0;
        }
           
        // load by id 
        $searchs = $this->_controller->get_context()->get_search_handle();
        $search_results = $this->_controller->get_context()->get_search_result_handle();
        
        $item = $searchs->get_by_id($id);
        
        if ($item) {
            $this->renderer->set_return('keyword', ($item->keyword));
          
            if ($item->c_count) {                 
                    
                /** @var \tf\core\collection_filter*/ 
                $f = $search_results
                    ->set_order('time DESC')
                    ->get_filter('/search/' . $id . '/')
                        ->set_config(array(
                          'where_sql'        => 'pid = ' . $item->id
                    ))
                    ->set_per_page($this->_per_page)
                    ->set_pagination($page);

                $this->renderer->set_return(
                    'posts', $f->apply()->as_array()
                );
                
            }
            else {
                $this->renderer->set_message(i18n::T('sat.search_not_found'), false);                
                return false;
            }
        }
        else {
            $this->renderer->set_return('keyword', '');
        }  
        
        
    }

}