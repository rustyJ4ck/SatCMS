<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: select.php,v 1.1.2.2 2012/06/09 08:52:48 Vova Exp $
 */  

class select_extrafs_field_item extends extrafs_field_item {
    
    
    function construct_after() {
        parent::construct_after();
        $value = (($v = $this->get_value()) && !empty($v['options'])) ? $v['options'] : false;
        $select_options = (!empty($value)) ? explode("\r", $value) : false;
        
        if (!empty($select_options))
        array_unshift($select_options, '@'); unset($select_options[0]); // key shift
        
        $this->select_options = $select_options;        
    }
    
    /** 
    * Create html control
    * @abstract create_control 
    */
    function create_html_control() {             
    
        $fvalue = $this->get_fvalue(); 
        
        $options = array();
        $options []= "<option value='0'>" . i18n::T('undefined') . "</option>";
        foreach ($this->select_options as $k => $v) {
            $selected = ($fvalue == $k) ? 'selected="selected"' :'';            
            $options []= "<option value='{$k}' {$selected}>{$v}</option>";
        }
        
        $options = join("\n", $options);
        
        return sprintf('<select name="_efs[%s][%s]" size="1">%s</select>'
            , ($this->get_group() ? $this->_group->name : '@fixme@')
            , $this->name
            , $options
        );

        
        
    }
    
}