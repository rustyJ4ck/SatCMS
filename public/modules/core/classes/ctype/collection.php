<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.14.1 2012/05/17 08:58:21 Vova Exp $
 */
class ctype_collection extends abs_collection {

    protected $_m_cache;

    protected $fields = array(
        'id'    => array('type' => 'numeric')
      , 'model' => array('type' => 'text', 'size' => 127)
      , 'title' => array('type' => 'text', 'size' => 127)
    );


    function from_array($a) {
        if (empty($a)) return;

        foreach ($a as $k => $v) {
            $item    = $this->alloc();
            $v['id'] = $k;
            $item->set_data($v);
            $this->append($item, $k);
        }
    }

    /**
     * @param string module.model
     * @return int id
     */
    function get_item_by_model($m) {
        if (isset($this->_m_cache[$m])) return $this->_m_cache[$m];

        // replace model namespace '\' to '.'
        $_m                 = str_replace('\\', '.', $m);
        $i                  = $this->get_item_by_prop('model', $_m);
        $this->_m_cache[$m] = $i;

        return $i;
    }
}

/**
 * Class ctype_item
 */
class ctype_item extends abs_collection_item {

    /**
     * @param bool $short
     * @return bool|false|object|string
     */
    function get_model($short = false) {
        return !$short
            ? $this->model
            : substr($this->model, strrpos($this->model, '.') + 1);
    }

    /**
     * @return abs_collection
     */
    function get_ctype_collection() {
        return core::selfie()->model($this->model);
    }
}