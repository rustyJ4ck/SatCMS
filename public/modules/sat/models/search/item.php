<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.1 2012/10/18 06:59:59 Vova Exp $
 */
 
class sat_search_item extends model_item {
    
    protected $_results;

    function construct_before() {
       $this->_results = core::module('sat')
        ->get_search_result_handle();
    }

    /**
    * Get results
    */
    function get_results($force = false) {
       if ($force) $this->load_results();
       return $this->_results;
    }

    /**
    * Load results
    */
    function load_results() {
       if ($this->_results->is_empty()) {
           $this->_results->clear()
            ->set_where('pid = %d', $this->id)
            ->load();
       }
    }

    /**
    * Remove
    */
    public function remove() {
        // Deletes links to post
        $this->get_results(true)->remove_all();
        return parent::remove();
    }
    
    /**
    * Make url for tag 
    */
    function make_urls() {
        $url = '/search/' . $this->id . '/';
        $this->append_urls('self', $url);
    }
     
}