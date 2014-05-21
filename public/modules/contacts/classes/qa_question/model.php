<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.1 2012/10/25 09:52:44 Vova Exp $
 */  

return
array(
    'fields' => array(
              'id'              => array('type' => 'numeric') 
            , 'site_id'         => array('type' => 'numeric') 
              
            , 'active'          => array('type' => 'boolean', 'default' => false)            
            
            , 'b_mod_notified'  => array('type' => 'boolean', 'default' => false)            
            , 'b_notify'        => array('type' => 'boolean', 'default' => false)            
              
            , 'title'           => array('type' => 'text')          
            , 'text'            => array('type' => 'text')    
            
            , 'url'             => array('type' => 'text',  'size' => 255
                , 'make_seo' => array('key' => 'title', 'strict' => 1)
                , 'space' => array('site_id')
                , 'autosave' => true
            )                  
              
            , 'phone'           => array('type' => 'text', 'size' => 255)        
            , 'email'           => array('type' => 'text', 'size' => 255)        
            
            , 'session_id'      => array('type'     => 'numeric', 'unsigned' => true) 
            , 'uid'             => array('type'     => 'numeric', 'unsigned' => true)
            
            , 'date'            => array('type' => 'unixtime', 'default' => 'now')
            
            , 'username'        => array('type' => 'text', 'size' => 255) 
            , 'c_count'         => array('type'     => 'numeric', 'unsigned' => true, 'autosave' => 1) 

    )
    , 'config' => array(
        'order_sql' => 'date DESC',
        
        'scheme' => array(
            'indexes' => array(
                'pid' => array('site_id', 'active', 'date')
        )
    )

        
    )        
);  
