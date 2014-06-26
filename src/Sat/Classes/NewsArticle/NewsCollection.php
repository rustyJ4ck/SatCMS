<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.3 2014/01/23 07:56:30 Vova Exp $
 */

namespace SatCMS\Sat\Classes\NewsArticle;

use core;
use SatCMS\Sat\Classes\Attachable\MasterCollection as MasterCollection;

/**
 * Class sat_news_collection
 */
class NewsCollection extends MasterCollection {

    const CTYPE = 'sat.news';

    /** @abstract */
    public $item_type = ''; // news|articles

    /** @abstract */
    public $category_model; // news_category

    protected $behaviors = array(
        'Sat.Commentable',
        'Sat.ImageAttachs',
        'Sat.RemoteImage'
    );

    /**
     * @param $id
     * @return $this
     */
    function load_for_site($id) {
        return $this->where('site_id', $id)->load();
    }

    function where_slug($slug) {
        return $this->where('slug', $slug);
    }

    function create_attachables() {
        $this->attachables->files = core::module('sat')->get_file_handle();
    }

    function construct_before(&$config) {
        $config['order_sql'] = 'created_at DESC';
    }

    function load_after() {
        // фильтрованный рендер
        if ($this->config->render_secondary) $this->load_secondary();
    }

    function render_yandex_rss() {
        $data = array();
        foreach ($this as $i) $data[] = $i->render_yandex_rss();
        return $data;
    }

    function render_novoteka_rss() {
        $data = array();
        foreach ($this as $i) $data[] = $i->render_novoteka_rss();
        return $data;
    }

    /**
     * Approve switch
     */
    function toggle_active($id, $value) {
        $this->update_item_fields($id,
            array('active' => $value)
        );
    }

    /**
     * @return \model_collection
     */
    function get_category_model() {
        return core::module('sat')->model($this->category_model);
        //return core::module('sat')->get_news_category_handle();
    }
}