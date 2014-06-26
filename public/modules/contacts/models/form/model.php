<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.3 2012/10/25 09:52:42 Vova Exp $
 */  

return
array(
'fields' => array(
          'id'                => array('type' => 'numeric') 
          
        , 'title'             => array('type' => 'text', 'size' => 255)

        , 'name'              => array('type' => 'text', 'size' => 255)
        , 'phone'             => array('type' => 'text', 'size' => 255)

        , 'message'           => array('type' => 'text', 'hidden' => true)
          
        , 'email'             => array('type' => 'text', 'size' => 255)        
        
        , 'uip'             => array('type'     => 'numeric', 'unsigned' => true, 'long' => true, 'hidden' => true)
        , 'uid'             => array('type'     => 'numeric', 'unsigned' => true, 'hidden' => true)
        
        , 'created_at'      => array('type' => 'unixtime', 'default' => 'now')

        //anket-result-id
        , 'result_id'       => array('type' => 'numeric', 'autosave' => true, 'hidden' => true)

        , 'b_confirmed'        => array('type' => 'boolean', 'default' => false
                                        , 'title' => 'Обработан', 'editable' => true
        )

)
, 'config' => array(
    'order_sql' => 'created_at DESC'
)        
);  
