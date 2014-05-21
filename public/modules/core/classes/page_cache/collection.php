<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2.6.1 2012/06/09 08:52:46 Vova Exp $
 */  
  


class page_cache_collection extends abs_collection {
    
       protected $_order_sql = 'access_time DESC';

       protected $fields = array(
              'id'               => array('type' => 'numeric')
            , 'access_time'      => array('type' => 'unixtime', 'default' => 'now')
            , 'counter'          => array('type' => 'numeric',  'default' => 1)

            , 'prev_access_time'     => array('type' => 'unixtime', 'default' => 'now')
            , 'sum_counter'          => array('type' => 'numeric',  'default' => 1)
            
            // cache expire time
            , 'expire_time'      => array('type' => 'unixtime', 'no_check' => true)
           
            , 'url_hash'         => array('type' => 'text', 'size' => 32)                   
            , 'url'              => array('type' => 'text', 'size' => 255) 
            
            , '_is_cached'       => array('type' => 'virtual')               
       );  

}