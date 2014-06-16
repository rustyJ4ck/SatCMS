<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

use SatCMS\Sat\Editor\Controllers\SatController;

class sat_news_controller extends SatController {

    protected $_where = 'site_id = %d'; // AND pid = %d';

    private $news_categories;

    protected $_limit = 10;

    function construct_after() {

        $this->_where = sprintf(
            $this->_where,
            $this->site_id
            //, $this->params->pid
        );

//        $this->filter_config

        /*
        if (empty($this->params->pid) && empty($this->params->sid)) {
            throw new controller_exception('Empty pid / no sid');
        }
        */


        //if ($this->mode == 'list') {
            $this->news_categories = $this->collection->get_category_model()->load_for_site($this->site_id);
            $this->response->categories = $this->news_categories->render();

        //dd($this->response->categories);
        //}

    }

    /**
     *
     */
    function render_after() {
       // dd($this->collection->get_last_query(),__METHOD__);
    }


}