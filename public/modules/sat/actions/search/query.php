<?php
      
/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: query.php,v 1.1.2.1 2012/10/18 06:59:59 Vova Exp $
 */

/**
* Suggest data
*/
class sat_search_query_action extends controller_action {
    
    private $_found = 0;
    
    // 604800-week
    private $_expire_time = 86400; // day
    
    function run() {       
        $q = $this->get_param('query');
                                                
        if (empty($q)) {
            $q = functions::request_var('keyword');
        }
        
        if (loader::in_ajax()) {
            $keyword  = trim($q); 
        } else {
            $keyword = (trim(urldecode($q)));  
        }     
        
        $this->renderer->set_return('keyword', $keyword);
        $this->renderer->set_main_title('search'); 

        if (empty($q)) return;
        
        if (strings::strlen($keyword) < 3) {
            $this->renderer->set_message('sat.search_too_short', array('result' => false));
            $this->renderer->set_ajax_message('sat.search_too_short');
            return false;
        }
        
        // make search and redirect to it
        $id = $this->make_search($keyword);
        
        // redirect to search results       
        
        $url = $this->_controller->get_context()->get_router()->make_url('/search/' . $id . '/');
        
        if (loader::in_ajax()) {
            
            $this->_controller->set_null_template();            
            $this->renderer
                ->set_ajax_message($this->_found ?
                    sprintf('По вашему запросу найдено %d записей', $this->_found)
                    : 'Подходящих записей не найдено'
                )
                ->set_ajax_result($this->_found)
                ->set_ajax_redirect($url);
        }
        else {
            functions::redirect($url);        
            core::get_instance()->halt();
        }
                                         
        
    }
    
    
    /**
    * Make search
    */           
    public function make_search($key) {
        
        $id = false;
        
        $psearchs = $this->_controller->get_context()->get_search_handle();
        $psearch_results = $this->_controller->get_context()->get_search_result_handle();
        
        $core = core::get_instance(); 
        
        $site_id = $this->_controller->get_context()->get_current_site_id();
        
        // check key exists
        
        if ($search_item = $psearchs->get_by_key($key, $site_id)) {
            $id = $search_item->id;
            $this->_found = $search_item->c_count;
        }
        
        // if too old, clean search results, make it new  
        
        if ($search_item) {
           if (($search_item->time + $this->_expire_time) < time()) {
               // clear
               $search_item->remove();
               $search_item = null;
           }
        }
        
        
        if (!$search_item) {
            
            $this->_found = 0;
            
            $cdata = core::module('sat')->get_node_handle();        
            
            $orig_key = $key;
            
            # remove this for non test
            # $key = strings::convert_from($key);
            
            $key =  core::lib('db')->escape(strings::strtolower($key));  
                  
            $cdata->set_where("site_id = {$site_id} AND active AND LCASE(title) LIKE '%{$key}%'")->load();
            
            $result = array();
            $ctype = $core->get_ctype('sat.node')->get_id();
            
            $this->_found = $cdata->count();
            
            if (!$cdata->is_empty()) {
                
                foreach ($cdata as $item) {
                    $result []= array(
                                  'title'       => $item->title                          
                                , 'description' => strip_tags($item->description)
                                , 'time'        => $item->updated_at
                                , 'url'         => $item->get_url()
                                , 'ctype'       => $ctype 
                                , 'post_id'     => $item->id           
                    );
                }
            
            }
            
            // create search history item
            $id = $psearchs->create(
                array(
                      'uid'       => $this->_controller->get_user()->id
                    , 'keyword'   => $key
                    , 'c_count'   => $this->_found
                    , 'site_id'   => $site_id
                )
            );
            
            // fill results                     
            foreach ($result as $v) {
                    $v['pid'] = $id;
                    $psearch_results->create($v);
            }    
        }
        
        return $id;

    }

}