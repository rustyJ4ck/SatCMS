<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: numeric.php,v 1.1.2.1 2012/05/17 08:58:22 Vova Exp $
 */

class boolean_extrafs_field_item extends extrafs_field_item {

    function efs_format_serialize($data) {
        return $data ? 1 : 0;
    }

    function efs_format_unserialize($data) {
        return $data ? 1 : 0;
    }

    /**
     * Create html control
     * @abstract create_control
     */
    function create_html_control() {

        $value = $this->get_fvalue();

        return

          sprintf('<input type="hidden" name="_efs[%s][%s]" value="0" />'
            , ($this->get_group() ? $this->_group->name : '@fixme@')
            , $this->name
            ) //fix empty checkboxes

        . sprintf('<input type="checkbox" name="_efs[%s][%s]" %s value="1" />'
            , ($this->get_group() ? $this->_group->name : '@fixme@')
            , $this->name
            , ($value ? "checked='checked'" : '')
        );



    }

}
