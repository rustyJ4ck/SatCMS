<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.1 2013/01/30 06:53:30 Vova Exp $
 */
 
class sat_widget_item extends abs_collection_item {
    
    /** 
    * Parse smarty data
    */
    function parse_data() {
        
        if ($this->plain) return;

        $pre = 'string: ' . $this->text;

        /** @var smarty */
        $tpl = core::lib('tpl_parser'); //tpl_loader::get_parser();
        
        $this->content = !empty($this->text)
            ? $tpl->fetch($pre)
            : '';
            
    }
}