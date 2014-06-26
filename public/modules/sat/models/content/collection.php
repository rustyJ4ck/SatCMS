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
class sat_content_collection extends NewsCollection {
//class sat_news_collection extends AttachableCollection {

    const CTYPE = 'sat.content';

    public $item_type = 'news';
    public $category_model = 'content_category';

    protected $behaviors = array(
        'Sat.Commentable',
        'Sat.ImageAttachs',
        'Sat.RemoteImage',
        'ExtraFS.Fields',
    );

    public $attachables = array(
        'files' => 'sat.file' /*'sat.node_file', 'sat.node_image' */
    );

    /** @var  sat_content_type_item */
    protected $type;

    /**
     * @return sat_content_type_collection
     */
    function get_types() {
        return core::module('sat')->get_content_types();
    }

    function set_type($type_id) {
        $type = $this->get_types()->get_item_by_id($type_id);

        if (!$type) {
            throw new collection_exception(__METHOD__ . ', Bad type: ' . $type_id);
        }

        $this->type = $type;
        $this->item_type = $type->slug;
    }

    function get_type() {
        return $this->type;
    }

    function get_categories() {
        return core::module('sat')->get_content_categories($this->type->id);
    }

    function where_slug($slug) {
        return $this->where('slug', $slug);
    }

    /*
    function create_attachables() {
        $this->attachables->files = core::module('sat')->get_file_handle();
    }
    */

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