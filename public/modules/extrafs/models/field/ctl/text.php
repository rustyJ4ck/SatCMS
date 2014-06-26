<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: text.php,v 1.1.2.1 2012/05/17 08:58:22 Vova Exp $
 */  

class text_extrafs_field_item extends extrafs_field_item {

    function construct_after() {
        parent::construct_after();

        $vf = $this->get_value();
        $class = $this->control_options->class;
        if (!empty($vf['wysiwyg'])) {
            $this->control_options->class = array_merge($class, array('wysiwyg'));
        }
    }

   /**
    * Create html control
    * @abstract create_control 
    */
    function create_html_control() {

        $rules = $this->get_html_rules();

        $vf = $this->get_value();

        $class = $this->get_html_class();

        if ($vf['rows'] > 1) {
            return sprintf("<textarea class='%s' name='_efs[%s][%s]' cols='%d' rows='%d' %s>%s</textarea>"
                , $class
                , ($this->get_group() ? $this->_group->name : '@fixme@')
                , $this->name
                , (!empty($vf['cols']) ? (int)$vf['cols'] : 80)
                , (!empty($vf['rows']) ? (int)$vf['rows'] : 50)
                , $rules
                , htmlspecialchars($this->get_fvalue())
                );
        }
        else {
            return sprintf('<input class="%s" name="_efs[%s][%s]" type="text" size="%d" value="%s" %s />'
                , $class
                , ($this->get_group() ? $this->_group->name : '@fixme@')
                , $this->name
                , (!empty($vf['cols']) ? (int)$vf['cols'] : 50) 
                , htmlspecialchars($this->get_fvalue())
                , $rules
            );
        }

            
        
    }


}
