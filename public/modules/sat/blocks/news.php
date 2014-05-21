<?php

/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: list.php,v 1.1.4.4 2013/11/12 06:50:20 Vova Exp $
 */

/**
 * Список
 */
class sat_news_block extends module_block {

    function construct_after() {
        $this->set_title('Новости');
        $this->set_template('news/list/default');
    }

    function run() {
        $count = $this->get_param('count', 3);
        return $this->context->get_news_handle()
            ->set_limit($count)
            ->load_for_site($this->context->get_current_site_id())
            ->render();
    }
}