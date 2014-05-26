<?php
  
/**
 * Searchs collection
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.1 2012/10/18 06:59:59 Vova Exp $
 */  
 
class sat_search_collection extends abs_collection {
    
       protected $fields = array(
          'id'                => array('type' => 'numeric')
        , 'site_id'           => array('type' => 'numeric')
        , 'keyword'           => array('type' => 'text', 'size' => 255)
        , 'uid'               => array('type' => 'numeric', 'unsigned' => true)  
        , 'time'              => array('type' => 'unixtime', 'default' => 'now')  
        
        /* global count cache
        */
        , 'c_count'           => array('type' => 'numeric', 'default' => 0, 'unsigned' => true)      
       );      
       
       
       /**
       * Check keyword exists
       * @return bool|searchs_item
       */
       function get_by_key($key, $site_id) {
           $this->set_where("keyword = '%s' AND site_id = %d", $key, $site_id)->load();
           
           if (!$this->is_empty()) return $this->get_item();
           return false;
       }
       
       /**
       * Get by id
       * @return bool|searchs_item
       */
       function get_by_id($key) {
           $this->set_where("id = %d", $key);
           $this->load();
           if (!$this->is_empty()) {
               $item = $this->get_item();
               //$item->load_results();
               return $item;               
           }
           return false;
       }       
       
       /**
       * Internal clearsil
       */
       function reset_counters() {
           $this->db->sql_query("UPDATE " . $this->get_table()  . " SET c_count = 0");     
       }            
       
}