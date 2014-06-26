<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.1 2012/10/25 09:52:43 Vova Exp $
 */  

return
array(
'fields' => array(
              'id'              => array('type' => 'numeric') 
            , 'pid'         => array('type' => 'numeric') 
              
            , 'active'          => array('type' => 'boolean', 'default' => false)            
            
            , 'title'           => array('type' => 'text')          
            , 'text'            => array('type' => 'text')          
              
            , 'phone'           => array('type' => 'text', 'size' => 255)        
            , 'email'           => array('type' => 'text', 'size' => 255)        
            
            , 'session_id'      => array('type'     => 'numeric', 'unsigned' => true) 
            , 'uid'             => array('type'     => 'numeric', 'unsigned' => true)
            
            , 'date'            => array('type' => 'unixtime', 'default' => 'now')
            
            , 'username'        => array('type' => 'text', 'size' => 255) 
)
, 'config' => array(
    'order_sql' => 'date DESC',
    'scheme' => array(
        'indexes' => array(
            'pid' => array('pid', 'date')
        )
    )
)        
);  
