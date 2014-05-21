<?php

/**
 * contentz blockz 
 * 
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: blocks.php,v 1.2 2010/07/21 17:57:16 surg30n Exp $
 */
  
class core_blocks extends module_blocks {
    
    /**
    * predefined blocks
    */
    protected $_blocks = array(
          'text' => array('template'  => false)
    );
     
    /**    
    * Text
    * @return text
    */
    function text($params = false) {
        if (empty($params->id)) return false;
        $cdata = $this->get_context()->get_text($params->id); 
        return $cdata->text;    
    }
}       
