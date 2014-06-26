<?php

/**
 * News collection
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.9 2013/10/02 07:38:27 Vova Exp $
 */

//use SatCMS\Sat\Classes\Attachable\MasterCollection as AttachableCollection;
use SatCMS\Sat\Classes\NewsArticle\NewsCollection;

/**
 * Class sat_news_collection
 */
class sat_news_collection extends NewsCollection {
//class sat_news_collection extends AttachableCollection {

    const CTYPE = 'sat.news';

    public $item_type = 'news';
    public $category_model = 'news_category';

    protected $behaviors = array(
        'Sat.Commentable',
        'Sat.ImageAttachs',
        'Sat.RemoteImage'
    );

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

    /*
    function get_category_model() {
        return core::module('sat')->get_news_category_handle();
    }
    */


}