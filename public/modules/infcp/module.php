<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: module.php,v 1.1.2.10.2.9 2013/01/30 06:53:29 Vova Exp $
 */

class tf_infcp extends core_module {

    function init0() {

        // Override dash title
        $this->i18n->set('Control panel', 'Личный кабинет');
        $this->i18n->set('Dashboard', 'Личный кабинет');

        return parent::init0();
    }

    function on_editor_after($c) {

        /**
         * Override user template
         */
        if ('root' === $this->renderer->get_page_template() && $this->auth->get_user()->level <= 1) {
            $this->renderer->set_page_template('root.user');
        }
    }

    /**
     * Dashboard overrid
     * @param editor_controller $controller
     */
    function on_editor_dashboard($controller) {

        $controller->set_template('../../modules/infcp/editor/templates/dashboard');

    }

}