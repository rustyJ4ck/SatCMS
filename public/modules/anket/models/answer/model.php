<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.1.2.2 2012/08/30 09:03:39 Vova Exp $
 */  

return
array(
          'id'                => array('type' => 'numeric')
        , 'text'              => array('type' => 'text')        
        , 'title'             => array('type' => 'text', 'size' => 255)        
        
        , 'value'             => array('type' => 'numeric')                  
        , 'b_valid'           => array('type' => 'boolean', 'default' => false)                  
        
        , 'pid'         => array('type' => 'numeric')  
	    , 'position'    => array('type' => 'position', 'space' => array('pid')) 
);  
