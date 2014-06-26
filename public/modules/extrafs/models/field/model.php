<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.4.2.4.3 2012/05/17 08:58:19 Vova Exp $
 */  

return array(   
    'fields' => array(
          'id'              => array('type' => 'numeric')
          
        , 'gid'             => array('type' => 'numeric')
                
        , 'title'           => array('type' => 'text', 'size' => 255)
        , 'name'            => array('type' => 'text', 'size' => 127, 'make_seo' => true)
        
        , 'class'           => array('type' => 'text', 'size' => 64)

        , 'description'     => array('type' => 'text', 'size' => 255)

	    , 'position'        => array('type' => 'position', 'space' => array('gid'))

	    , 'type'            => array('type' => 'numeric')

	    , 'value'           => array('type' => 'text', 'no_format' => true)       
        
        , 'control'         => array('type' => 'virtual')            
    )
    ,  
    'config' => array(
    //    'table'         => '%class%'
    ),

    'formats' => array(
        'editor' => array(
            'list' => array(
                'gid'         => array('hidden' => true),
                'control'     => array('hidden' => true),
                'description' => array('hidden' => true),
                'title'       => array('editable' => true),
            )
        )
    )
);
