<?php

/**
 * Nodes Controller
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: sat_node.php,v 1.1.2.6.2.7 2012/12/20 06:47:30 Vova Exp $
 */

class core_dashboard_controller extends editor_controller {

    protected $with_model = false;

    //
    function action_index() {

        if (!loader::in_ajax()) {
            throw new editor_exception('Not allowed ' . __METHOD__);
        }

        $this->set_layout('embed');
        $this->set_template('partials/index');
        //dd(__METHOD__);
    }

    function action_test() {

        $this->response->hello = 'yes';

    }
}