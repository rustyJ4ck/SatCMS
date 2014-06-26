<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1 2008/05/22 07:58:08 surg30n Exp $
 */  
  
class users_payments_collection extends model_collection {

      protected $_order_sql = 'time DESC';
    
      protected $fields = array(
          'id'          => array('type' => 'numeric')
        , 'uid'         => array('type' => 'numeric')
        , 'value'       => array('type' => 'numeric')
        , 'gate'        => array('type' => 'text')
        , 'time'        => array('type' => 'unixtime', 'default' => 'now')
       );  

}