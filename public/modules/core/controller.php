<?php

/**
 * Core contoller
 * 
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: controller.php,v 1.7.6.1 2012/09/14 06:20:57 Vova Exp $
 */
 
/**
* @package core
*/

class core_controller extends module_controller {

    /**
     * i18n all constants
     */
    function action_api_editor_i18n() {

        $this->renderer
            ->set_ajax_answer(
                $this->context->get_core()->get_langwords()
            )
            ->ajax_flush();
    }

    /**
     * Editor nav menu
     */
    function action_api_editor_menu() {

        /** @var tf_editor $ed */
        $ed = core::lib('editor');

        $menu = array();

        // dies on get_editor_actions
        $menu ['core'] = core::get_instance()->get_editor_actions();

        foreach (core::modules() as $module) {
            $menu [$module->get_name()]= $module->get_editor_actions();
        }

        $menuNormalized = array();

        foreach ($menu as $key => $actions) {

            $submenuNormalized = array();

            if (!empty($actions)) {

                foreach ($actions as $subKey => $subMenu) {

                    if (!empty($subMenu['url'])) $subMenu['url'] = $ed->make_url($subMenu['url'], 1);

                    $submenuNormalized []=
                        !$subMenu
                        ? array() //separator
                        : array_merge(array(
                            'id' => $subKey),
                             $subMenu
                            )
                    ;
                }

                $menuNormalized []= array(
                    'id' => $key,
                    'title' => i18n::T(array($key,'_name')),
                    'actions' => $submenuNormalized
                );

            }
        }

        $this->renderer
            ->set_ajax_answer($menuNormalized)
            ->ajax_flush();



    }

}

