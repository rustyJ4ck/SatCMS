<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2 2010/07/21 17:57:16 surg30n Exp $
 */  
  

class texts_collection extends model_collection {

       protected $key = 'name';  
                                 
       protected $fields = array(
          'id'               => array('type' => 'numeric')
        , 'name'             => array('type' => 'text',         'make_seo' => true)
		, 'text'             => array('type' => 'text',          
                                 'format' => false   )
	    , 'title'	      	 => array('type' => 'text')
       );  


}