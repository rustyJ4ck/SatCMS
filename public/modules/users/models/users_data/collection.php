<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1 2008/03/29 10:13:10 surg30n Exp $
 */  
  
class users_data_collection extends model_collection {
    
      protected $fields = array(
          'id'          => array('type' => 'numeric')
        , 'pid'         => array('type' => 'numeric')
        , 'key'         => array('type' => 'text')
        , 'value'       => array('type' => 'unixtime')
        
     /* , 'data'       => array('type' => 'text')
        // autoloaed
     */
       );  
}