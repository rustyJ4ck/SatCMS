<?php

 return array(
          'id'          => array('type' => 'numeric')
        , 'title'       => array('type' => 'text', 'size' => 64, 'editable' => true)
        , 'name'        => array('type' => 'text', 'size' => 64, 'hidden' => true)
        , 'c_users'     => array('type' => 'numeric', 'autosave' => true)
        
        
        , 'acls'        => array('type' => 'virtual', 'method' => 'acls')   
 ); 