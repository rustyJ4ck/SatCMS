<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

use SatCMS\Sat\Editor\Controllers\SatController;


/**
 * Class sat_content_controller
 * @property sat_content_collection collection
 */
class sat_content_controller extends SatController {

    protected $_where = 'site_id = %d'; // AND pid = %d';

    private $_categories;
    private $_type;

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
        /*
            $this->_categories = $this->collection->get_category_model()->load_for_site($this->site_id);
            $this->response->categories = $this->_categories->render();
        */
        //dd($this->response->categories);
        //}

        if ($this->params->op == 'edit') {
            $this->params->type_id = $this->_load_id()->type_id;
        }

        $type = $this->request->postget('type_id') ?: $this->params->type_id;

        if ($type) {
            $this->collection->set_type($type);
            $this->_type = $this->context->get_content_types()->get_item_by_id($type);
            $this->base_url .= ('&type_id=' . $type);

        } else {
            $this->params->op = 'index2';
            $this->with_model = false;
        }
    }

    function run_before() {

        $this->response->types = $this->context->get_content_types()->render();

        if ($this->_type) {
            $this->response->categories = $this->collection->get_categories()->render();
            $this->response->type = $this->_type->render();
        }

    }

    function action_index2() {
        $this->set_template('content/index2');
    }



}