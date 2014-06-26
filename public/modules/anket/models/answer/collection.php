<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.1.2.1 2012/09/14 18:38:36 j4ck Exp $
 */  
  
class anket_answer_collection extends model_collection {
    
        /**
        * Approve switch
        */
        function toggle_valid($id, $value) {  
            $this->update_item_fields($id, 
                array('b_valid' => $value)
            );                      
        }   

    
}