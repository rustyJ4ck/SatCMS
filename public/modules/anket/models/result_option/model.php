<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.2 2012/09/14 18:49:47 j4ck Exp $
 */  

return
array(
    'fields' => array(
              'id'                => array('type' => 'numeric')
            , 'title'             => array('type' => 'text',        'size' => 255)
            , 'name'             => array('type' => 'text',         'size' => 255)
            , 'text'              => array('type' => 'text')        
            
            , 'score_low'          => array('type' => 'numeric')                  
            , 'score_high'         => array('type' => 'numeric')                  
                                                        
            , 'pid'     => array('type' => 'numeric')  
            
            , 'b_valid'           => array('type' => 'boolean', 'default' => false)    
    )
    , 'config' => array(
        'order_sql' => 'score_low'
    )   
);