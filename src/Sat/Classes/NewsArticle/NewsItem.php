<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.3 2014/01/23 07:56:30 Vova Exp $
 */

namespace SatCMS\Sat\Classes\NewsArticle;

use SatCMS\Sat\Classes\Attachable\MasterItem as MasterAttachableItem;

/*
use sat_file_collection;
use sat_node_file_collection;
use sat_node_image_collection;
*/

use core;

/**
 * Class NewsItem
 * @property NewsCollection container
 */
class NewsItem extends MasterAttachableItem {

    /** @var  CategoryItem */
    protected $_category;

    protected $_similar;

    function load_secondary($options = null) {

        (is_array($options) && empty($options['category'])) or $this->get_category();
        // (is_array($options) && empty($options['similar'])) or $this->get_similar();

        return parent::load_secondary($options);
    }

    protected function _item_type() {
        return $this->container->item_type;
    }

    protected function _category_model() {
        return $this->container->category_model;
    }

    /**
     * @return \news_category
     */
    function get_category() {
        if (!isset($this->_category) && $this->pid) {
            $this->_category = core::module('sat')->get_managed_item($this->_category_model(), $this->pid);
        }

        return $this->_category;
    }

    /**
     * Similar
     * @return \model_collection
     */
    function get_similar() {
        if (!isset($this->_similar) && $this->pid) {
            $this->_similar = clone $this->get_container();
            $this->_similar->set_where('pid = %d', $this->pid)
                ->append_where('id <> ' . $this->id)
                ->set_order('created_at DESC')
                ->set_limit(5)
                ->load();
        }

        return $this->_similar;
    }

    /**
     * Remove
     */
    function remove_after() {
        $this->sync_count();

        // if item is new, this fails
        //$this->get_attachs()->remove_all();
        //$this->get_attach_images()->remove_all();

        parent::remove_after();
    }

    function sync_count() {
        $handle = $this->container->get_category_model();

        if ($handle && is_callable(array($handle, 'sync_children'))) {
            $handle->sync_children();
        } else {
            core::dprint(__METHOD__ . ' sync_count not callable');
        }
    }

    /**
     * @param $data
     */
    function modify_after($data) {

        // newb - sync

        if (!$data['id']) {
            $this->sync_count();
        }

        if (@$data['twit_me']) {
            $this->make_urls();
            $this->twit_me();
        }

        parent::modify_after($data);
    }

    /**
     * Make url for tag
     */
    function make_urls() {
        $cat     = $this->get_category();
        $cat_url = $cat ? ($cat->slug . '/') : '';

        // router::make_url appends current domain to url, so
        $url = /*core::module('content')->get_router()->make_url*/
            (
                '/'
                . $this->_item_type()
                . '/'
                . $cat_url
                . $this->slug . '.html');

        $this->append_urls('self', $url);

        /** @var \tf_editor $ed */
        $ed = core::lib('editor');
        $this->append_urls('editor_edit', $ed->make_ng_url('?m=sat&c=news&op=edit&id=' . $this->id . '&site_id=' . $this->site_id, 1));

        parent::make_urls();
    }

    /**
     * twitter
     */
    function twit_me() {
        $url = $this->get_url('self');

        /* generate short url */
        $purls = core::selfie()->get_url_handle();
        $purl  = $purls->create_url($url);

        $url = $purl->get_url('uri');

        $twit = $this->title
            . '. ' . (strlen($this->description) > 45 ? (substr($this->description, 0, 45) . '…') : '')
            . ' #' . (($c = $this->get_category()) ? $c->title : '')
            . ' ' . $url;

        core::dprint('twitter: ' . $twit);

        /** @var tf_twitter */
        $twitter = core::lib('twitter');

        return $twitter->post_status($twit);

    }

    /**
     * @return array
     */
    function render_yandex_rss() {
        $data                = $this->render();
        $data['created_at'] = date('d.m.Y H:i', $this->created_at);

        $data['yandex_text'] = strip_tags(preg_replace(
            '@<img .*/>@U', ''
            , $data['text']
        ));

        unset($data['text']);

        return $data;
    }

    /**
     * @param mixed $data
     */
    function render_after($data) {
        $data['category'] = $this->_category ? $this->_category->render() : false;
        $data['similar']  = $this->_similar  ? $this->_similar->render() : false;
        parent::render_after($data);
    }


}