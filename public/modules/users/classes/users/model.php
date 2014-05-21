<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.2 2010/07/21 17:57:18 surg30n Exp $
 */    
  
return array(        
      'id'         => array('type' => 'numeric')
      
    , 'gid'        => array('type' => 'numeric', 'hidden' => true)
      
    , 'login'      => array('type' => 'text',
                            'filter' => array('params' => array('BEGINS', 'AND'))
      )

    , 'password'   => array('type' => 'text', 'hidden' => true)
    , 'nick'       => array('type' => 'text')

    , 'email'      => array('type' => 'text',
                            'filter' => array('params' => array('BEGINS', 'AND'))
      )
    
    , 'active'     => array('type' => 'boolean',            'default' => true, 'editable' => true)
    
    , 'date_reg'   => array('type' => 'unixtime'        , 'default' => 'now',
                            'autosave' => true,
                            'filter' => array('params' => array('BETWEEN', 'AND'))
      )

    , 'last_login' => array('type' => 'unixtime'        , 'default' => 'now', 'autosave' => true)
    
    // , 'avatar'     => array('type' => 'virtual')
    
    , 'gender'     => array('type' => 'numeric',            'default' => 0, 'hidden' => true)
    , 'level'      => array('type' => 'numeric',            'default' => 1, 'hidden' => true)
    
    , 'avatar'     => array('type' => 'image'
                            , 'storage' => 'avatars'
                            , 'format' => array('resize', 256, 256, 'inside', 'down')
                            , 'thumbnail' => array(
                                    'format' => array('crop', 'center', 'center', 96, 96)
                                )
        )
    
    , 'c_balance'  => array('type' => 'numeric',            'default' => 0, 'autosave' => true, 'hidden' => true)
    
    // payd user
    , 'payd_user'  => array('type' => 'boolean',            'default' => false, 'autosave' => true, 'hidden' => true)
    , 'payd_till'  => array('type' => 'unixtime',           'autosave' => true, 'hidden' => true)
    
    // virtual
    , 'gender_string'       => array('type' => 'virtual', 'hidden' => true)
    , 'level_string'        => array('type' => 'virtual')
    , 'gender_translated'   => array('type' => 'virtual', 'hidden' => true)
    , 'level_translated'    => array('type' => 'virtual', 'hidden' => true)
                                              
    , 'acls' => array('type' => 'virtual', 'method' => 'acls', 'hidden' => true)
);  