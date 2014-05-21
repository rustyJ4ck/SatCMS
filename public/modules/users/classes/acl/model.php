<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.2 2010/07/21 17:57:23 surg30n Exp $
 */  

 return array(
          'id'          => array('type' => 'numeric')
        , 'uid'         => array('type' => 'numeric')   
        
        // user=0, group=1
        , 'type'        => array('type' => 'numeric', 'size' => 1, 'default' => 0, 'unsigned' => true)
        
        , 'section'     => array('type' => 'text', 'size' => 32)
        , 'section_id'  => array('type' => 'numeric', 'default' => 0) // item id
        
        , 'action'      => array('type' => 'numeric')        
        , 'allow'       => array('type' => 'boolean', 'default' => false)
 ); 