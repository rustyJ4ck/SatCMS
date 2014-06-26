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
        , 'name'              => array('type' => 'text',        'make_seo' => 'title')        
        , 'text'              => array('type' => 'text')        
        , 'footer_text'       => array('type' => 'text')
        
        , 'notify_email'      => array('type' => 'text',        'size' => 255)
        
        /** баллы для успешного теста */
        , 'value'                => array('type' => 'numeric')
        
        , 'site_id'     => array('type' => 'numeric')  
	    , 'position'    => array('type' => 'position', 'space' => array('site_id'))  
        
);  
