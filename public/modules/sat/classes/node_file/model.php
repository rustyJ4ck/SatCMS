<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.2 2011/02/24 09:17:44 Vladimir Exp $
 */

return
    array('fields'  =>

          array(
              'id'        => array('type' => 'numeric')

              , 'pid'         => array('type' => 'numeric')
              , 'position'    => array('type' => 'position', 'space' => 'pid')

              , 'sid'         => array('type' => 'numeric', 'unsigned' => true, 'title' => 'Attach-SID')
              , 'ctype_id'    => array('type' => 'numeric', 'autosave' => true, 'default' => 200, 'title' => 'Attach-CT')

              , 'title'       => array('type' => 'text', 'size' => 127)
              , 'comment'     => array('type' => 'text', 'size' => 1024)

              , 'created_at' => array('type' => 'unixtime', 'default' => 'now', 'autosave' => true)

              , 'file'        => array('type' => 'file'
                  , 'spacing'                     => 1
                  , 'storage'                     => 'files'
              )

          ),

          'formats' => array(
              'editor' => array(
                  'list' => array(
                      'pid'          => array('hidden' => true),
                      'comment'      => array('hidden' => true),

                      'sid'          => array('hidden' => true),
                      'ctype_id'     => array('hidden' => true),

                      'title'        => array('editable' => true),
                  )
              )
          )
    );
  