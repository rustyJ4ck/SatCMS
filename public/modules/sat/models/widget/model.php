<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.1 2013/01/30 06:53:30 Vova Exp $
 */  

return
array('fields' =>
array(
          'id'          => array('type' => 'numeric')

        , 'text'        => array('type' => 'text')

        , 'title'       => array('type' => 'text', 'size' => 255)
        , 'name'        => array('type' => 'text', 'size' => 255)

        , 'class'       => array('type' => 'text', 'size' => 255)
        , 'raw'         => array('type' => 'boolean', 'default' => false, 'title' => 'Wrap')
        , 'plain'       => array('type' => 'boolean', 'default' => false, 'title' => '!Smarty')
        , 'active'      => array('type' => 'boolean', 'default' => false)

        , 'pid'         => array('type' => 'numeric')
	    , 'position'    => array('type' => 'position', 'space' => array('pid'))

        // 'content'    => parsed template
),

      'formats' => array(
          'editor' => array(
              'list' => array(
                  'class'  => array('hidden' => true),
                  'text'   => array('hidden' => true),
                  'pid'    => array('hidden' => true),

                  'title'  => array('editable' => true),
                  'name'   => array('editable' => true),

                  'plain'  => array('editable' => true),
                  'raw'    => array('editable' => true),
                  'active' => array('editable' => true),
              )
          )
      )
);
