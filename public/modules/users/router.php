<?php

/**
 * Router for users
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: router.php,v 1.5.6.2 2012/09/13 06:58:57 Vova Exp $
 */

/**
 * Class users_router
 */
class users_router extends module_router {

    /**
     * Route users
     * @return string template
     */
    function route($parts) {

        // Try default routes
        if (false !== ($response = parent::route($parts))) {
            return $response;
        }

        $layout = $this->context->config->get('template', 'root');

        // request
        $r = new stdClass();

        $r->action = 'users';
        $r->user   = '';

        // [cp][profile]
        $count = count($parts);

        // users actions              
        if (1 == $count) {
            $r->action = $parts[0];
            $parts     = array();
        } else
        // users/cp/action/*params////*
        if ('cp' == @$parts[0] && $count > 1) {
            $layout           = $this->context->config->get('cp_template', 'root');
            $r->action        = 'cp';
            $r->option        = $parts[1];
            $r->option_params = count($parts > 2) ? array_splice($parts, 2) : array();
            $parts            = array();
        }

        $count = count($parts);

        if (1 == $count) {
            $r->user = $parts[0];
        }

        /** @var users_controller */
        $controller = $this->controller;

        $response = $controller->run($r);

        $this->renderer
            ->set_page_template($layout)
            ->set_main_template(
                $controller->get_template()
            );

        return $response;
    }

}