<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2 2008/03/28 08:50:11 j4ck Exp $
 */  
  
class links_collection extends abs_collection {

       protected $fields = array(
              'id'               => array('type' => 'numeric')
            , 'src'              => array('type' => 'numeric')
            , 'dst'              => array('type' => 'numeric')
            , 'type'             => array('type' => 'numeric')
            
       );  

}