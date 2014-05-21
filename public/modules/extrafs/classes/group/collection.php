<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.1.2.6 2013/01/30 06:53:29 Vova Exp $
 */

/*

Update scenario

/// Item::modify_after()

        if (core::get_modules()->is_registered('extrafs')) {  
            $flds = $this->get_extrafs_fields();
            --> core::module('extrafs')->get_groups_w_fields($ids, $this);
                --> extrafsmodule->get_group_handle()->load_children()
    
            $flds->sat_update_fields($data['_efs']);
            -->  $this->get_fields();
                 $this->_fields->update_field_values($data);
                --> $fvs = $this->get_field_values();  
                    create or update $value            
        }



*/

class extrafs_group_collection extends abs_collection {

    /** @var abs_collection_item */
    protected $_parent;

    function set_parent($p) {
        if (!isset($p)) throw new collection_exception('Empty parent in extrafs_group_collection::set_parent');
        $this->_parent = $p;

        return $this;
    }

    function get_parent() {
        return $this->_parent;
    }

    /**
     * load field values for group
     * @param mixed parent ot null
     */
    function get_field_values($p = null) {

        if (isset($p)) $this->set_parent($p);

        /** @var extrafs_group_item $g */
        foreach ($this as $g) {
            $g->get_field_values();
        }

        return $this;
    }

    /**
     * update fields
     * data['group']['field']
     *
     * @param mixed $data
     */
    function sat_update_fields($data) {
        core::dprint('group_collection::sat_update_fields', core::E_DEBUG5);
        foreach ($this as $g) {
            $g->sat_update_fields(@$data[$g->name], $this->get_parent());
        }
    }

}

/**
 * Group item
 */
class extrafs_group_item extends abs_collection_item {

    /** @var extrafs_field_collection */
    protected $_fields;

    function get_fields($parent = null) {
        if (!isset($this->_fields)) {

            $this->_fields = core::module('extrafs')->get_field_handle()
                ->set_where('gid = %d', $this->id)
                ->set_parent($parent ? $parent : $this->get_container()->get_parent())
                ->load();

            foreach ($this->_fields as $f) {
                $f->set_group($this);
            }

        }

        return $this->_fields;
    }

    function remove_after() {
        $this->get_fields()->remove_all();
    }

    function load_secondary($options = null) {
        $this->get_fields();

        //$this->_fields->load_secondary();
        return $this;
    }

    function render_after($data) {
        if (isset($this->_fields)) {
            if (!core::in_editor()) $this->_fields->is_render_by_key('name');
            $data['fields'] = $this->_fields->render();
        }
    }

    /**
     * Get fields values, if not present, create empty
     * @return extrafs_value_collection
     */
    function get_field_values() {

        $fields = $this->get_fields();

        // @fixme bad things happend if !$fields
        return $fields ? $fields->get_field_values() : false;
    }

    /**
     * update fields
     * data['field']
     *
     * @param mixed $data
     */
    function sat_update_fields($data, $parent) {

        // core::var_dump(get_class($this->get_container()->get_parent()));

        $this->get_fields();

        if (!$this->_fields->count()) return $this;

        core::dprint(array('group_item::sat_update_fields (%s)', $this->name), core::E_DEBUG4);

        $this->_fields->update_field_values($data);

        /*
        foreach ($this->_fields as $f) {
           $g->updaget_field_values();
        }
        */

        // foreach ($this as $i) {
        //$i->sat_update_fields(@$data[$g->name]);
        // }
    }

    function make_urls() {

        if (core::in_editor()) {
            ///extrafs/field/
            $this->append_urls('fields', sprintf('?m=extrafs&c=field&gid=%d', $this->id));
        }

    }
}        