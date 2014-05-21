<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

use SatCMS\Modules\Sat\Editor\Controllers\SatController;

class test_article_category_controller extends SatController {

    protected $_where = 'site_id = %d';

    function construct_after() {

        $this->_where = sprintf(
            $this->_where,
            $this->site_id
        );
    }


}