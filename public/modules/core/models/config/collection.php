<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2.6.2 2013/12/19 09:15:34 Vova Exp $
 */
class config_collection extends model_collection {

    protected $_render_key = 'name';

    protected $fields = array(
        'id'     => array('type' => 'numeric')
    , 'name'     => array('type' => 'text', 'make_seo' => true, 'size' => 64, 'editable' => true)
    , 'title'    => array('type' => 'text', 'size' => 127, 'editable' => true)
    , 'value'    => array('type' => 'text', 'editable' => true)
    , 'b_system' => array('type' => 'boolean', 'default' => true, 'editable' => true)
    );

    /**
     * Merge with other config
     */
    function merge_with(&$data) {

        if (!$this->is_empty()) {

            $this->is_render_by_key(true);

            foreach (($t = $this->render()) as $k => $v)
                if (!isset($data[$k])) $data[$k] = $v['value'];

            $this->is_render_by_key(false);
        }

        return $this;

    }

    /**
     * Create/update param
     */
    function update_param($key, $value) {
        if (empty($key)) throw new core_exception('Try insert nul config key');
        $item = $this->get_item_by_prop('name', $key);
        $id   = $item ? $item->id : false;
        $this->modify(array('name' => $key, 'value' => $value), $id);
    }

    /**
     * Approve switch
     */
    function toggle_system($id, $value) {
        $this->update_item_fields($id,
            array('b_system' => $value)
        );
    }

}