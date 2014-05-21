<?php

/**
 * Controller 
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: sat_site.php,v 1.1.2.3.2.3 2012/09/12 13:01:11 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

class sat_site_controller extends editor_controller {
    
    protected $title = 'Сайты';

    function construct_after() {
        $this->response->templates = $this->context->get_templates();
    }

    function action_clear_static() {
        $site = $this->_load_id();
        if ($site) {
            $site->clear_static();
            $this->ajax_answer(true, 'Статичный кэш сброшен', $site->id);
        }                           
    }
    
    function action_build_static() {
        $site = $this->_load_id();
        if ($site) {
            $site->clear_static();
            $this->ajax_answer(true, 'Запущен генератор статики', $site->id);
        }                           
    }

}  

