<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.3 2011/02/24 09:17:44 Vladimir Exp $
 */

/*
       , 'sphere_id'      => ['type' => 'relation', 'relation' => ['model' => 'resellers.psphere', 'key' => 'id', 'where' => []]]
        , 'vendor_id'      => ['type' => 'relation', 'relation' => ['model' => 'resellers.vendor']]
        , 'type_id'        => ['type' => 'relation', 'relation' => ['model' => 'resellers.producttype']]

 */

     
return array(

    'fields' => array(

          'id'          => array('type' => 'numeric')
        , 'title'       => array('type' => 'text', 'size' => 127)
        , 'ddomain'     => array('type' => 'text', 'size' => 127)     // domain in debug
        , 'domain'      => array('type' => 'text', 'size' => 127)
        , 'aliases'     => array('type' => 'text', 'size' => 255)
        , 'path'        => array('type' => 'text', 'size' => 127)
        , 'template'    => array('type' => 'text', 'size' => 127)

        , 'owner_id'    // => array('type' => 'numeric', 'default' => 1)
            => array(
                'type' => 'relation', 'relation' => array('model' => 'users.users')
            )

        , 'html_title'  => array('type' => 'text', 'size' => 255)
        , 'description' => array('type' => 'text', 'no_format' => true)
        , 'text'        => array('type' => 'text', 'no_format' => true)
        , 'md' => array('type' => 'text', 'size' => 255)
        , 'mk' => array('type' => 'text', 'size' => 255)

        , 'b_static'    => array('type' => 'boolean', 'default' => false)
        , 'b_default'   => array('type' => 'boolean', 'default' => false, 'editable' => true)

        , 'active'      => array('type' => 'boolean', 'default' => true, 'json' => false, 'editable' => true)

        , 'image'     => array(
            'type'          => 'image'
            , 'title'       => 'Логотип'
            , 'storage'     => 'sites'
            , 'thumbnail'   => array(
                    'format' => array('crop', 'center', 'center', 64, 64)
                )
            )

    ),

    'formats' => array(

        'site' => array(

        ),

        'editor' => array(

            'default' => array(
            ),

            'list' => array(
                  'template'    => array('hidden' => true)
                , 'owner_id'    => array('hidden' => true)
                , 'html_title'  => array('hidden' => true)
                , 'description' => array('hidden' => true)
                , 'text'        => array('hidden' => true)
                , 'md'          => array('hidden' => true)
                , 'mk'          => array('hidden' => true)
                , 'b_static'    => array('hidden' => true)
            ),

            'form' => array(
            )
        )

    )

);
