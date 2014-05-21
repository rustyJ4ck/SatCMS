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
class sat_news_category_block extends module_block {

    function construct_after() {
        $this->set_title('Категории');
        $this->set_template('news/category/default');
    }

    function run() {
        return $this->context->get_news_category_handle()->load_for_site(
            $this->context->get_current_site_id()
        )->render();
    }
}