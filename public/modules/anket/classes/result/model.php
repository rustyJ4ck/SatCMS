<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.1 2011/11/09 09:35:12 Vova Exp $
 */  

return
array(
'fields' => array(
          'id'                => array('type' => 'numeric')
        , 'comment'           => array('type' => 'text')        
        , 'results'           => array('type' => 'text', 'no_format' => true)        
        
        // html result
        , 'text'           => array('type' => 'text', 'no_format' => true)        
        
        , 'value'             => array('type' => 'numeric')                  
        , 'b_valid'           => array('type' => 'boolean', 'default' => false)                  
        
        , 'pid'             => array('type' => 'numeric')  
        
        , 'phone'           => array('type' => 'text', 'size' => 255)        
        , 'name'            => array('type' => 'text', 'size' => 255)        
        , 'email'           => array('type' => 'text', 'size' => 255)        
        
        , 'uip'             => array('type'     => 'numeric', 'unsigned' => true, 'long' => true ) 
        , 'uid'             => array('type'     => 'numeric', 'unsigned' => true)
        
        , 'date'            => array('type' => 'unixtime', 'default' => 'now')

)
, 'config' => array(
    'order_sql' => 'date DESC'
)        
);  
