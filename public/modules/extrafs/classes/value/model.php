<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.4.1.2.2 2012/06/09 08:52:49 Vova Exp $
 */  

return array(   
    'fields' => array(
          'id'              => array('type' => 'numeric')
          
        , 'pid'             => array('type' => 'numeric')
        , 'fid'             => array('type' => 'numeric')

        , 'ctype_id'        => array('type' => 'numeric',
                                     'size' => 2,
                                     'default' => 200, 'autosave' => true)
            
        , 'value'           => array('type' => 'text', 'no_format' => true)
    )
      ,  
    'config' => array(
        'table'         => '%class%'
        , 'order_sql'   => false
    )
);
