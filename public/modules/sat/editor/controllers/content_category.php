<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

use SatCMS\Sat\Editor\Controllers\SatController;

class sat_content_category_controller extends SatController {

    protected $_where = 'site_id = %d';

    function construct_after() {

        $this->_where = sprintf(
            $this->_where,
            $this->site_id
        );

        $this->response->types = $this->context->get_content_types()->render();
    }

    function render_after() {
    //     dd($this->collection->get_last_query(),__METHOD__);
    }

}