<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: module.php,v 1.1.4.2.2.2 2013/01/30 06:53:29 Vova Exp $
 */

// @deprecated
// require "modules/extrafs/abstract/collection.php";

class tf_extrafs extends core_module {

    private $_cached_fields = array();

    /** for autocomplete sake */

    /** @return extrafs_value_collection */
    function get_value_handle() {
        return $this->model('value');
    }

    /** @return extrafs_group_collection */
    function get_group_handle() {
        return $this->model('group');
    }

    /** @return extrafs_field_collection */
    function get_field_handle() {
        return $this->model('field');
    }

    /*
       1. SatCMS\ExtraFS\Classes\Behaviors\Fields::load_secondary_after
       2. SatCMS\ExtraFS\Classes\Behaviors\Fields::get
       3. tf_extrafs::get_groups_w_fields
    */

    /**
     * @param array $ids - array(id1, id2, ...)
     * @param model_item $parent
     * @return extrafs_group_collection
     */
    function get_groups_w_fields($ids, $parent = null) {

        if (empty($ids)) return false;

        if (!$parent) {
            throw new module_exception(
                __METHOD__ . ' without parent'
            );
        }

        $ret = $this->get_group_handle();

        if ($parent) {
            $ret->set_parent($parent);
        }

        foreach ($ids as $id) {
            $g = $this->get_group($id);
            if ($g) {
                $ret->append($g);
                $g->get_fields();
            }
        }

        return $ret;
    }

    /**
     * @param $id
     * @return extrafs_group_item
     */
    function get_group($id) {
        return $this->get_managed_item('group', $id);
    }

    /**
     * load fields for ctype/pid
     * @j4ck: ctype_pid? wtf
     */
    function get_fields($ctype_id, $pid = 0) {

        if (is_string($ctype_id)) {
            $ct = $this->get_core()->get_ctype($ctype_id);
            if (!$ct) throw new module_exception('bad ctype in get_fields : ' . $ctype_id);
            $ctype_id = $ct->get_id();
        }

        if (!isset($this->_cached_fields[$ctype_id][$pid])) {
            $this->_cached_fields[$ctype_id][$pid] =
                $this->get_field_handle()
                    ->set_where('ctype = %d', $ctype_id)
                    ->append_where($pid ? array('ctype_pid = %d', $pid) : false)
                    ->load();
        }

        return clone $this->_cached_fields[$ctype_id][$pid];
    }

    /**
     * @todo move this to sat-module
     * Render before event
     * @param sat_node_item $item
     */

    /*
    function on_sat_render_before(sat_node_item $item) {

        $template = $item->get_template();
        if (!empty($template["site"]["secondary"]["list"])) {
            // render children extrafields in list
            core::dprint('site-secondary-list:yes load secondary for children');
            $item->get_children()->load_secondary();
        }
    }
    */
}
