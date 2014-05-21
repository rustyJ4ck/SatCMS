<?php
/**
 * Messanger
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: messenger.php,v 1.2 2010/07/21 17:57:17 surg30n Exp $
 */    
 
 require_once "modules/core/libs/messenger/generic.php";
 
 /**
 * Exception
 */          
 class tf_messanger_exception  extends tf_exception {
 }
 
 /**
 * Messanger
 */
 class tf_messenger {
       
     private $_transports = array(
          'icq'
        , 'email'
     );  
      
     // private $_transport_handle = array();

            
     /**
     * @return tf_messanger_generic
     * 
     * @param mixed $id
     */
     function get($id) {         
         $object = null;
         require_once "modules/core/libs/messenger/{$id}.php";
         $class = "tf_messenger_{$id}";
         $object = new $class ();
         return $object;         
     }
 }
