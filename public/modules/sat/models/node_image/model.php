<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.4 2011/11/17 08:56:36 Vova Exp $
 */

/*
'original'   => ['action' => ['crop', 'center', 'center', 320, 320]], //, 'outside', 'down'
'thumbnail'   => ['action' => ['crop', 'center', 'center', 64, 64]], //, 'outside', 'down'
*/

return array(

    'fields'    =>

        array(
            'id'       => array('type' => 'numeric')

            , 'pid'        => array('type' => 'numeric')
            , 'position'   => array('type' => 'position', 'space' => 'pid')

            , 'sid'        => array('type' => 'numeric', 'unsigned' => true, 'title' => '@SID')
            , 'ctype_id'   => array('type' => 'numeric', 'autosave' => true, 'default' => 200, 'title' => '@CT')

            , 'title'      => array('type' => 'text', 'size' => 127)
            , 'comment'    => array('type' => 'text', 'size' => 1024)

            , 'created_at' => array('type' => 'unixtime', 'default' => 'now', 'autosave' => true)

            , 'alt_image'  => array('type' => 'image'
            , 'spacing'                    => 1 // set space to 2, if you has too much pics
            , 'storage'                    => 'images'
            , 'format'                     => array('crop', 'center', 'center', 1024, 768)
            , 'remote'                     => true
                // , 'thumbnail'   => array(125,165)
            )
                // big image?
            , 'image'      => array('type' => 'image'
            , 'spacing'                    => 1 // set space to 2, if you has too much pics
            , 'storage'                    => 'images'
            , 'format'                     => array('resize', 800, 600, 'inside', 'down')
            , 'remote'                     => true
            , 'thumbnail'                  => array(
                    'format' => array('crop', 'center', 'center', 64, 64)
            )
        )

        ),

    'behaviors' => array(
        'sat.remoteImage'
    ),

    'formats'   => array(
        'editor' => array(

            'list' => array(
                'pid'      => array('hidden' => true),
                'comment'  => array('hidden' => true),

                'sid'      => array('hidden' => true),
                'ctype_id' => array('hidden' => true),

                'title'    => array('editable' => true),
            ),

            'form' => array(
                'image' => array('description' => 'Описание изображения')
            )
        )
    )
);