<?php
  
/**
 * @package    satcms
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.4.1 2012/05/17 08:58:22 Vova Exp $
 */  
  
return array(
    
    'fields' => array(

          'id'                => array('type' => 'numeric')

        , 'pid'               => array('type' => 'numeric', 'hidden' => true)

        , 'tpid'              => array('type' => 'numeric', 'hidden' => true)

        , 'ctype_id'          => array('type' => 'numeric')

        , 'user_id'           => array('type' => 'numeric')

        , 'user_ip'           => array('type' => 'numeric',
                                       'unsigned' => true,
                                       'long' => true)

        , 'username'          => array('type' => 'text',
                                       'size' => 255)
        , 'text'              => array('type' => 'text')

        , 'created_at'       => array('type' => 'unixtime',
                                       'default' => 'now',
                                       'autosave' => true)

        , 'c_rating'          => array('type' => 'numeric')

        , 'deleted'           => array('type' => 'boolean',
                                       'default' => 0,
                                       'autosave' => true,
                                       'editable' => true)

        /*
        // virtuals:

        , 'user'                // user object

        , 'user_ip_string'
        , 'level'
        , 'close_levels'

        , 'childrens'           // count
        , 'childrens_ids'       // array(id, ...)

        , 'rating_disabled'     // when rating done

        */
    
    ),

     'formats' => array(
        'editor' => array(
            'list' => array(

                'parent' => array('type'  => 'virtual',
                                  // 'class' => 'fit',
                                  'title' => 'Parent',
                                  'method' => 'parent'
                )
            )
        )
    )
    
    ,
    
    'config' => array(
        'table'     => '%class%'
        , 'order_sql' => 'created_at'
    )
);  