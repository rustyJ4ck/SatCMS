<?php

/**
 * Pages collection
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.2 2013/10/22 08:46:12 Vova Exp $
 */

namespace SatCMS\Sat\Classes\NewsArticle;

abstract class CategoryCollection extends \model_collection {


    /** @abstract string  */
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

        $sql = array("UPDATE %s p1 SET p1.c_count = (SELECT COUNT(*) FROM %s p2 WHERE p2.cat_id = p1.id)"
        , $this->get_table()
        , $this->connection()->get_prefix() . $this->child_model
        );

        if ($id) {
            $sql[0] .= sprintf(" WHERE p1.id = %d;", $id);
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
