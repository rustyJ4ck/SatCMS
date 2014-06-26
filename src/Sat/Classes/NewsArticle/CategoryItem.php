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

abstract class CategoryItem extends \model_item {

    protected $_items;

    /**
     * Make url
     */
    function make_urls() {

        $url = core::module('sat')->get_router()->make_url(
             '/'
            . $this->container->child_model
            .'/'
            . $this->slug
            . '/'
        );

        $this->append_urls('self', $url);
    }


    /**
     * Load news
     *
     * @param mixed $num
     */
    /*
    function fetch_content($num = 2) {

        $this->_items =
            core::module('content')
                ->get_news_handle()
                ->set_where("cat_id = %d", $this->get_id())
                ->set_limit($num)
                ->set_cfg_var('render_ios', true)
                ->set_working_fields($this->container->content_fields())
                ->load();


         return $this->_items;
    }
    */

}