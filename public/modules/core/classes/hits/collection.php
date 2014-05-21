<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2 2008/03/28 08:50:10 j4ck Exp $
 */  
  

class hits_collection extends abs_collection {

       protected $fields = array(
          'id'                => array('type' => 'numeric')
        , 'title'             => array('type' => 'text')
	    , 'date'	      => array('type' => 'unixtime')
	    , 'data'	      => array('type' => 'text')
       );  


}