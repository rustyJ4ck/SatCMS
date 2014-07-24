<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: image.php,v 1.1.2.1 2012/05/17 08:58:22 Vova Exp $
 */  

class image_extrafs_field_item extends extrafs_field_item {
    
    function efs_format_load($data) {
        $vf = $this->get_value(); 
        $vf['type'] = $this->type_string;
        // $vf['storage'] = $vf['path'];
        $this->get_container()->format_field_on_load($vf, $data);                
        return $data;
    }
    
    function efs_format_modify($data) {  
        $vf = $this->get_value(); 
        $vf['type'] = $this->type_string;
        // $vf['storage'] = $vf['path'];
        $current = $this->get_fvalue();
        $this->get_container()->format_field_on_modify($vf, $data, $current);
        return $data;
    }
    
    function efs_format_serialize($data) {
        return $data ? serialize($data) : '';
    }    
    
    function efs_format_unserialize($data) {
        return $data ? @unserialize($data) : '';
    }
    
    /** 
    * Create html control
    * @abstract create_control 
    */
    function create_html_control() {

        $field = $this->get_value();
        $value = $this->get_fvalue();  //;$this->get_data('fvalue');

        return Html::File(array(
            'title' => $field['title'],
            'name'  => sprintf('_efs[%s][%s]', ($this->get_group() ? $this->_group->name : '@fixme@'), $this->name),
            'value' => $value
        ));
        
        $uid = uniqid('_efs');
    
        return sprintf('<span id="%s"><input name="_efs[%s][%s]" type="file" size="40"/> %s</span>'
            , $uid
            , ($this->get_group() ? $this->_group->name : '@fixme@')
            , $this->name
            , (isset($fvalue['url']) ? "<a href='{$fvalue['url']}' target='_blank' class='mediaflink'>Открыть</a>, " : '')
        )

        .
        
        (!isset($fvalue['url']) ? '':
        sprintf(
        <<<MECK
        <a href="javascript:;" onclick="$('#{$uid}').parent().append('<input type=hidden name=_efs[%s][%s] value=remove />
        <div class=help forered>Файл помечен к удалению</div>');$('#{$uid}').remove();$(this).hide();">Удалить</a>
MECK
            , ($this->get_group() ? $this->_group->name : '@fixme@')
            , $this->name
        ))
        ;
        
    }
    
    function remove_after() {
        // remove file, if any
        $current = $this->get_fvalue();
        if (!empty($current)) {
            $vf = $this->get_value(); 
            $vf['type'] = $this->type_string;
            $this->get_container()->format_field_on_remove($vf, $current, $current);
        }
        return parent::remove_after();
    }
    
}
