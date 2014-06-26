<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.2.2.2 2012/06/09 08:52:48 Vova Exp $
 */

/**
 * Class extrafs_field_collection
 */
class extrafs_field_collection extends model_collection {

    /** @var model_item */
    protected $_parent;

    /** @var extrafs_value_collection */
    protected $_extra_fs_values_collection; // optimize load

    protected static $_types = array(
          1   => 'numeric'
        , 2   => 'text'
        , 3   => 'datetime'
        , 4   => 'select'
        , 5   => 'boolean'
        , 6   => 'file'
        , 7   => 'image'
        , 8   => 'link'
        , 100 => 'sat_node'
    );

    static function get_types() {
        return self::$_types;
    }

    static function get_type($id) {
        return @self::$_types[$id];
    }


    /**
     * set parent collection_item
     * @return $this
     */
    function set_parent($p) {
        $this->_parent = $p;

        return $this;
    }

    function get_parent() {
        return $this->_parent;
    }

    function modify_before(&$data) {
        if (!empty($data['value'])) $data['value'] = serialize($data['value']);
    }

    /**
     * get values for mapped parent
     * @return extrafs_value_collection
     */
    function get_field_values() {

        if (!isset($this->_extra_fs_values_collection)) {
            // fetch values for current parent *pid*
            $this->_extra_fs_values_collection = core::module('extrafs')
                ->get_value_handle()
                ->set_where('pid = %d AND ctype_id = %d'
                    , $this->_parent->get_id()
                    , $this->_parent->get_ctype_id()
                )
                ->load();
        }

        foreach ($this as $v) {
            $v->get_field_value();
        }

        return $this->_extra_fs_values_collection;
    }

    /**
     * update fields
     * data['field']
     *
     * @param mixed $data
     */

    /*
   function sat_update_fields($data, $parent) {
       $this->get_fields();
       if (!$this->_fields->count()) return $this;
       
       core::dprint(array('group_item::sat_update_fields (%s)', $this->name), core::E_DEBUG4);        
       
       $this->_fields->sat_update_fields($data, $parent);
       
       foreach ($this->_fields as $f) {
          $g->updaget_field_values();
       }    

       // foreach ($this as $i) {
            //$i->sat_update_fields(@$data[$g->name]);
       // }
   }  
   */

    /**
     * update fields for parent item
     */
    function update_field_values($data) {

        core::dprint('efs_collection::update_field_values', core::E_DEBUG4);

        foreach ($this as $v) {
            $fv = $v->get_field_value();

            $value = $v->efs_format_modify(@$data[$v->name]);
            $value = $v->efs_format_serialize($value);

            if (!$fv) {
                // create one
                $fv             = $this->_extra_fs_values_collection->alloc();
                $fv->ctype_id   = $this->get_parent()->get_ctype_id();
                $fv->pid        = $this->get_parent()->get_id();
                $fv->fid        = $v->get_id();
            } else {
                // update                
            }

            $fv->value = $value;
            $fv->save();

            ///debug
            core::dprint(array('ex_ [%d%s] %s %s ', $fv->id, ($fv->is_new() ? '+' : '-'), $v->name, $fv->value));

            $v->get_field_value(true);
        }

        core::dprint_r($this->_extra_fs_values_collection->as_array());

        ///debug
        core::dprint(array('ex_ count %d ', $this->_extra_fs_values_collection->count()));
    }

    function get_extra_fs_values_collection() {
        return $this->_extra_fs_values_collection;
    }


    /** render according to [ctype_pid] = [] */
    function render_by_pid() {
        $data     = $this->render();
        $new_data = array();

        if (!empty($data))
            foreach ($data as $v) {
                if (!isset($new_data[$v['ctype_pid']])) $new_data[$v['ctype_pid']] = array();
                $new_data[$v['ctype_pid']][] = $v;
            }

        return $new_data;
    }

    /**
     * Build custom control hook
     */
    protected function alloc_before(&$class, &$config, &$data, &$verified) {

        if (!empty($data['type'])) {
            $type  = self::get_type($data['type']);
            $class = $type . '_' . $class;
            if (!class_exists($class, 0)) {
                require __DIR__ . '/ctl/' . $type . '.php';
            }
        }
    }

}


/**
 * Field item
 */
class extrafs_field_item extends model_item {

    /** @var aregistry  */
    protected $control_options = array(
        'class' => array('form-control'),
        'rules' => array()
    );

    /*
     fvalue = mapped field value (rendered abs_item)     
    */

    /**
     * @var extrafs_group_item
     */
    protected $_group;

    protected $_field_value;

    protected $_in_editor;

    /** @var array unpacked @value (field props) */
    protected $real_value;

    function construct_before(&$data) {
        $this->_in_editor = core::in_editor();
        $this->control_options = new aregistry($this->control_options);
    }

    function construct_after() {

        $this->real_value = false;

        if (!empty($this->value)) {
            $this->real_value = unserialize($this->value);
        }

        $this->type_string = '';

        if ($this->type) {
            $this->type_string = extrafs_field_collection::get_type($this->type);
        }
    }

    function get_group() {
        /** @todo load group if not used with @see set_group */
        return $this->_group;
    }

    function set_group($g) {
        $this->_group = $g;
    }

    function in_editor() {
        return $this->_in_editor;
    }

    function render_before($data) {
        $data['control'] = $this->control = $this->create_html_control();
    }

    function efs_format_serialize($data) {
        return $data;
    }

    function efs_format_unserialize($data) {
        return $data;
    }

    /** @abstract */
    function efs_format_load($data) {
        return $data;
    }

    /**
     * @abstract
     * Called on item update
     *
     * @return string serialized data
     */
    function efs_format_modify($data) {
        /*
         switch ($f->type_string) {
            case 'numeric':
                $v = @$f->value_a['float'] ? floatval($v) : intval($v);
                break;
            case 'boolean':
                $v = $v ? 1 : '';
                break;
            case 'unixtime':
                $v = strtotime($v);
                break;
            case 'select':
                $v = intval($v);
                break;
        }
        return $v;
        */
        return $data;
    }

    /**
     *
     */
    function get_html_rules() {

        $rules = $this->control_options->rules;

        if (!empty($rules)) {
            $_rules = array();
            foreach ($rules as $rule) {
                $_rules []= 'data-rule-' . $rule;
            }
            $rules = join("\n", $_rules);
        } else {
            $rules = '';
        }

        return $rules;
    }

    function get_html_class() {
        $class = $this->control_options->class;
        if (!empty($class)) {
            $class = join(' ', $class);
        } else {
            $class = '';
        }

        return $class;
    }

    /**
     * Create html control
     * @abstract create_control
     */
    function create_html_control() {

        $fvalue = $this->get_fvalue();
        $value = $this->get_value();
        $rules = $this->get_html_rules();

        return sprintf('<input type="text" class="%s" name="_efs[%s][%s]" value="%s" size="%d" %s />'
            , $this->get_html_class()
            , ($this->get_group() ? $this->_group->name : '@fixme@')
            , $this->name
            , $fvalue
            , (@$value['size'] ?: 40)
            , $rules
        );

        /*
        switch ($this->type_string) {
            case 'select':
            $control['options'] = empty($this->real_value['options']) ? false : explode("\n", $this->real_value['options']);
            foreach ($control['options'] as &$v) {
                $v = preg_replace("@\r$@", '', $v);
            }
            break;
        }
        
        $this->set_data('control', $control);
        */
    }

    /**
     * @return array field properties!
     */
    function get_value() {
        return $this->real_value;
    }

    /**
     * Get field value
     * @return mixed data
     */
    function get_fvalue() {
        return $this->get_data('fvalue');
    }

    /** get unloaded value */
    function get_field_value_ex() {
        $this->container->get_field_values();

        return $this->get_field_value();
    }

    /**
     * get related field value for parent item
     *
     * whereis PID!!!
     * @return extrafs_value_item
     */
    function get_field_value($force = false) {

        if (!$this->get_container()->get_extra_fs_values_collection()) return false;

        /*
         clone-error-fix test:
         var_dump($this->get_container()->get_parent()->get_id());
        */

        if (!isset($this->_field_value) || $force) {
            /*
                $p = $this->get_container()->get_parent();
                $p->get_id();
                $this->_field_value =
                $this->container->get_module()->get_value_handle()
                    ->set_limit(1)
                    ->set_where('fid = %d AND pid = %d', $this->id, $p->id)
                    ->load()
                    ->get_item();
            */
            $this->_field_value =
                $this->get_container()
                    ->get_extra_fs_values_collection()
                    ->get_item_by_prop('fid', $this->id);

            core::dprint(array('try fid: %d : %s', $this->id, $this->_field_value ? '+' : '-'));

            /** FORMATTED VALUE */
            $this->fvalue =
                $this->efs_format_load($this->_field_value ? $this->efs_format_unserialize($this->_field_value->value) : null);

            // core::var_dump($this->fvalue);
        }

        return $this->_field_value;
    }

    function render_after($data) {

        // $value = $this->get_field_value();

        /*
        $data['fvalue'] = '';
        
        if ($value) {
        
        $data['fvalue'] = $this->container->_format_field_load($this, $value ? $value->value : '');
        
        if ($this->type_string == 'text') 
            $data['fvalue'] = ($data['fvalue']); // fix ajax json calls
            
        }
        */

        $data['value'] = $this->real_value;
    }

    function prepare2edt_before($data) {
        $data['value'] = $this->real_value;
    }

    function remove_after() {
        if (!$this->container->get_parent()) return;

        $fval = $this->get_field_value_ex();
        if ($fval) $fval->remove();
    }

    function make_urls() {

        if (core::in_editor()) {
            $this->append_urls('parent', '?m=extrafs&c=group');
        }

    }

}
