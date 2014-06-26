<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.1 2013/01/30 06:53:30 Vova Exp $
 */  
  
class sat_widget_collection extends model_collection {

   /**
    * Approve switch
    */
    function toggle_active($id, $value) {  
        $this->update_item_fields($id, 
            array('active' => $value)
        );                      
    } 
    
    /** 
    * Parse smarty data
    */
    function parse_data() {
        $this->invoke('parse_data');
        return $this;
    }
    
}