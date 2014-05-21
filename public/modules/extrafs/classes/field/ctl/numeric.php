<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: numeric.php,v 1.1.2.1 2012/05/17 08:58:22 Vova Exp $
 */  

class numeric_extrafs_field_item extends extrafs_field_item {


    function efs_format_serialize($data) {
        return $data ? strval($data) : '';
    }    
    
    function efs_format_unserialize($data) {
        return $data ? floatval($data) : 0;
    }    
    
    function efs_format_modify($data) {  
        $vf = $this->get_value(); 
        $vf['type'] = $this->type_string;
        $current = $this->get_fvalue();
        $this->get_container()->format_field_on_modify($vf, $data, $current);

        if (!empty($vf['precision'])) {
            $data = round($data, $vf['precision']);
        }
        return $data;
    }
        
}
