<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.1 2011/11/09 09:35:11 Vova Exp $
 */  

return
array(
          'id'                => array('type' => 'numeric')
        , 'title'             => array('type' => 'text',        'size' => 255)
        , 'text'              => array('type' => 'text')        
        
        , 'value'             => array('type' => 'numeric')                  
        
        , 'pid'     => array('type' => 'numeric')  
	    , 'position'    => array('type' => 'position', 'space' => array('pid')) 
);  
