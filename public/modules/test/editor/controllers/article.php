<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

use SatCMS\Sat\Editor\Controllers\SatController;

class test_article_controller extends SatController {

    protected $collection_config = array('with_module_prefix' => 1);

    protected $_where = 'site_id = %d'; // AND pid = %d';

    private $categories;

    protected $_limit = 10;

    function construct_after() {

        $this->_where = sprintf(
            $this->_where,
            $this->site_id
            //, $this->params->pid
        );


        //if ($this->mode == 'list') {
            $this->categories = $this->collection->get_category_model()->load_for_site($this->site_id);
            $this->response->categories = $this->categories->render();

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