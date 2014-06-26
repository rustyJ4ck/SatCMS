<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

use SatCMS\Sat\Editor\Controllers\SatController;

class sat_news_category_controller extends SatController {

    protected $collection_config = array('with_module_prefix' => 1);

    protected $_where = 'site_id = %d';

    function construct_after() {

        $this->_where = sprintf(
            $this->_where,
            $this->site_id
        );
    }

    function render_after() {
    //     dd($this->collection->get_last_query(),__METHOD__);
    }

}