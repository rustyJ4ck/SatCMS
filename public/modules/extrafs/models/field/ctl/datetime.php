<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: link.php,v 1.1.2.1 2012/05/17 08:58:22 Vova Exp $
 */

class datetime_extrafs_field_item extends extrafs_field_item {

    function construct_after() {

        parent::construct_after();

        $this->control_options->class = array_merge($this->control_options->class, array('datetime'));

        $this->control_options->set('rules.number', true);

    }

}