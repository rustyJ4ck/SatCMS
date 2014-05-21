<?php

/**
 * Comments
 *
 * @package    satcms
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.4.1 2012/05/17 08:58:22 Vova Exp $
 */


/**
 * @todo refactor old code
 * Class sat_comment_item
 */
class sat_comment_item extends abs_collection_item {

    protected static $_parents_cache = array();
    /** @var  rates_collection */
    protected $_rates;
    /** @var  user_item */
    protected $_user;
    /** @var  abs_collection_item */
    protected $_parent;

    function load_secondary($options = null) {

        if ((is_array($options) && in_array('user', $options) || $options === true)) {

            if (!isset($this->_user)) {
                $this->_user = core::module('users')->get_user($this->user_id);
            }
        }

        return $this;
    }

    /**
     * Rate
     * @return bool rate result
     */
    public function rate($value) {

        // @todo make thru core!
        // rate($pid, $value, $type = 'post')
        $result = $this->get_rates()->rate($this->id, $value, comments_collection::CTYPE);

        if ($result) {
            // update counter
            $this->c_rating += $value;
            $this->get_container()->update_item_fields($this->id, array('c_rating' => $this->c_rating));
        }

        // @todo disable when user already votes for this item
        $this->disable_rating();

        return $result;
    }

    /**
     * Rates
     */
    public function get_rates() {
        return $this->_rates;
    }

    /**
     * Disable rate for item
     */
    public function disable_rating() {
        $this->rating_disabled = true;
    }


    function render_after($data) {

        $data['user_ip'] = $this->get_user_ip();

        if ($this->_user) {
            $data['user'] = $this->_user->render();
        }

    }

    function get_user_ip() {
        return long2ip($this->user_ip);
    }

    /**
     * Render
     */

    /*
        public function render_before() {
                                                 
            $this->user_ip_string  = $this->get_user_ip();

        }
    */
    function render_parent() {
        $this->get_parent();
        $this->parent = $this->_parent ? $this->_parent->render() : false;
    }

    /**
     * Get attached post
     */
    function get_parent() {

        if ($this->pid && !isset($this->_parent)) {

            // try container parent
            $this->_parent = $this->get_container()->get_parent();

            if (!isset($this->_parent)) {

                if ($this->_parent = array_get(static::$_parents_cache, $this->ctype_id . '.' . $this->pid)) {

                } else {
                    $core  = core::selfie();
                    $ctype = $core->get_ctype($this->ctype_id, false);

                    /*
                     * dd($core->get_ctype($this->ctype_id, false)->get_model());
                     */

                    // has-ctype
                    if ($ctype) {
                        $this->_parent = $core
                            ->model($core->get_ctype($this->ctype_id, false)->get_model())
                            ->load_only_id($this->pid);
                    }

                    array_set(static::$_parents_cache, $this->ctype_id . '.' . $this->pid, $this->_parent);
                }

            }

        }

        return $this->_parent;
    }

    /**
     */
    function modify_after() {
        core::get_instance()->event('comment', $this);
    }

    /**
     */
    function remove_after() {
        $p = $this->get_parent();
        if ($p && method_exists($p, 'comment_removed')) $p->comment_removed($this);
        core::get_instance()->event('comment_remove', $this);
    }

    function virtual_parent($type) {

        if ($type == 'view') {

            return $this->get_parent()
                ? sprintf('<a href="%s" target="_blank">%s</a>',
                    $this->_parent->get_url('self'),
                    ($this->_parent->id . '| ' . $this->_parent->title)
                )
                : i18n::T('deleted');

        }


    }

    /**
     * Make urls for item
     * {$post.url}/comment/{$comment.id}/plus/
     */
    function make_urls() {
        $post = $this->get_parent();
        if ($post && $post instanceof IAbs_Collection_Item) {
            //@todo check make_url
            $url = $post->get_url('self');
            $this->append_urls('self', $url . '#comment' . $this->id);
            $this->append_urls('minus', $url . '/comment/' . $this->id . '/minus/');
            $this->append_urls('plus', $url . '/comment/' . $this->id . '/plus/');
        }
    }

    /**
     * Get assigned user
     */
    protected function get_user() {
        return $this->_user;
    }

    /**
     * dependences
     * @throws tf_exception
     */
    private function make_depend() {

        // rates
        $this->_rates = core::module('content')->get_rates_handle();

        // core::get_instance()->class_register('rates', array('no_preload' => true), true);
        // $this->_rates->load_for_post($this->id);
    }

}