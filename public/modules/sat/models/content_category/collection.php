<?php

/**
 * Pages collection
 *
 * @todo use abstract class from src
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.2 2013/10/22 08:46:12 Vova Exp $
 */

class sat_content_category_collection extends model_collection {

    public $child_model = "news";

    /** used in fetch_content */
    protected $content_fields = false;

    function where_slug($slug) {
        return $this->where('slug', $slug);
    }

    /**
     * @param $id
     * @return $this
     */
    function load_for_site($id) {
        return $this->clear()->set_where('site_id = %d', $id)->load();
    }

    /**
     * @param int $id
     */
    function sync_children($id = 0) {

        // sqlite: UPDATE sat_sat_news_category SET c_count = (SELECT COUNT(*) FROM sat_sat_news p2 WHERE p2.pid = sat_sat_news_category.id)

        $collection = core::module('sat')->model($this->child_model);

        if (!$collection) {
            throw new collection_exception('Bad model in category: ' . $this->child_model);
        }

        $table = $this->get_table();
        $table_model = $collection->get_table();

        $sql = "UPDATE {$table} SET c_count = (SELECT COUNT(*) FROM {$table_model} p2 WHERE p2.pid = {$table}.id)";


        if ($id) {
            $sql .= " WHERE id = {$id}";
        }

        $this->db->query($sql);
    }

    function content_fields($f = null) {
        if (!is_null($f)) {
            if (!is_array($f)) $f = array();
            $this->content_fields = $f;

            return $this;
        }

        return $this->content_fields;
    }

    function fetch_content($num = 2) {
        $this->invoke('fetch_content', 2);

        return $this;
    }

}
