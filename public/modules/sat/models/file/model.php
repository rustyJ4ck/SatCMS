<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.3 2011/02/24 09:17:43 Vladimir Exp $
 */  
     
return array(
      'id'          => array('type' => 'numeric')

    , 'pid'         => array('type' => 'numeric', 'hidden' => true)

    , 'sid'         => array('type' => 'numeric', 'unsigned' => true, 'title' => 'Attach-SID', 'hidden' => true)
    , 'ctype_id'    => array('type' => 'numeric', 'autosave' => true, 'default' => 200, 'title' => 'Attach-CT', 'hidden' => true)

    , 'title'       => array('type' => 'text', 'size' => 127, 'hidden' => true)
    , 'created_at' => array('type' => 'unixtime', 'default' => 'now', 'autosave' => true, 'hidden' => true)

    , 'file' => array(
                  'type' => 'file'
                , 'storage'  => 'files'
                , 'spacing'  => 1
    )

    // autocreate thumbs for files

    , 'thumbnail'   => array(
          'type'     => 'image'
        , 'storage'  => 'files/thumbs'
        , 'spacing'  => 1
        , 'title'    => ''
        ,  'format' => array('resize', 800, 600, 'inside', 'down')
        ,  'thumbnail' => array('format' => array('crop', 'center', 'center', 128, 96))
    )
);
  
  // 'storage' => 'posts/files', 'thumbnail' => array(96, 96), 'max_width' => 1024
  // , 'allow' => array('jpg', 'png', 'gif', 'jpeg'))