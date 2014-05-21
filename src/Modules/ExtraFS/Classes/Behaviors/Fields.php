<?php

/**
 * ExtraFSable
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.9 2013/10/02 07:38:27 Vova Exp $
 */

namespace SatCMS\Modules\ExtraFS\Classes\Behaviors;

use core;
use extrafs_group_collection;

/**
 * Class Fields
 *
 * Model-item must implement get_extrafs_ids()
 */
class Fields extends \model_behavior {

    /** @var extrafs_group_collection */
    protected $fields;

    /** @var bool obsolete check */
    protected $has_extra = false;

    function configure() {
        $this->has_extra = core::modules()->is_registered('extrafs');

        if (!method_exists($this->model, 'get_extrafs_ids')) {
            throw new \collection_exception('extrafs behavior: model must implement get_extrafs_ids() in ' . get_class($this->model));
        }
    }

    function load_secondary_after($options) {
        (is_array($options) && empty($options['extrafs'])) or $this->get();
    }

    function get_ids() {
        return $this->model->get_extrafs_ids();
    }

    /**
     * Load groups+fields+values
     * @return extrafs_group_collection
     */
    function get() {

        if (!isset($this->fields) && ($this->has_extra)) {

            // get efs group ids for node
            $ids = $this->get_ids();

            // if (!empty($template['extrafs']) && ($ids = array_keys($template['extrafs'])) && !empty($ids)) {

            if (!empty($ids)) {

                /** @var \tf_extrafs $m */
                $m = core::module('extrafs');
                $this->fields = $m->get_groups_w_fields($ids, $this->model);

                // load values, for existing item
                if (!$this->model->is_new()) {
                    $this->fields->get_field_values();
                }
            }

        }

        return $this->fields;
    }

    /**
     * @param $data
     */
    function render_after($data) {

        if ($this->fields) {
            if (!core::in_editor()) $this->fields->is_render_by_key('name');
            $data['extrafs'] = $this->fields->render(); // [group].fields.[name]
        }

    }

    /**
     * Prepare for editor
     * @param mixed $data
     */
    function prepare2edt_before($data) {

        /*
        var_dump('prepare2edt_before',
            core::get_modules()->is_registered('extrafs'),
            $data['template_a']
            );
        */

        if ($this->get()) {
            $data['extrafs'] = $this->fields->render();
        }

    }

    /**
     * After submit
     * @param mixed $data
     */
    function modify_after($data) {
        if ($this->has_extra && ($flds = $this->get()) && isset($data['_efs'])) {
            $flds->sat_update_fields($data['_efs']);
            core::dprint('node::modify_after', core::E_DEBUG5);
        }
    }

    function remove_before() {

        // remove values, not fields!!!
        $gfs = $this->get();

        if ($gfs && $gfs->count()) {
            /** @var \extrafs_group_item $g */
            foreach ($gfs as $g) {
                $g->get_field_values()->remove_all();
            }
        }
    }

}