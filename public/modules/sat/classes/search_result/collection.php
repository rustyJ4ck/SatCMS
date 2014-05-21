<?php
  
/**
 * Searchs collection
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.1 2012/10/18 06:59:59 Vova Exp $
 */  

class search_result_collection extends abs_collection {

       protected $fields = array(
          'id'             => array('type' => 'numeric')
        , 'pid'            => array('type' => 'numeric', 'unsigned' => true)  
        , 'post_id'        => array('type' => 'numeric', 'unsigned' => true)  
        , 'ctype'          => array('type' => 'numeric', 'unsigned' => true)  
        , 'description'    => array('type' => 'text', 'size' => 255)
        , 'title'          => array('type' => 'text', 'size' => 255)    
        , 'url'            => array('type' => 'text', 'size' => 255)    
        , 'time'           => array('type' => 'unixtime')             
       );      
}