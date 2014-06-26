<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.4.13 2014/01/23 07:56:30 Vova Exp $
 */
 
use SatCMS\Sat\Classes\NewsArticle\NewsItem;
 
class sat_content_item extends NewsItem {

    /**
     * extrafs.fields behavior
     * @return array|null [id1, id2, ...]
     */
    function get_extrafs_ids() {
        return array(1, 2);
        // return $this->extra_fields;
    }

    function get_type() {
        return $this->type_id ? $this->container->get_types()->get_item_by_id($this->type_id) : false;
    }

    /**
     * @todo make with container->get_categories()
     * @return sat_content_category
     */
    function get_category() {
        if (!isset($this->_category) && $this->pid) {
            $this->_category = core::module('sat')->get_managed_item($this->_category_model(), $this->pid);
        }

        return $this->_category;
    }

}