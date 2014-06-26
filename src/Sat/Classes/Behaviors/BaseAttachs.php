<?php

/**
 * Commentable
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.9 2013/10/02 07:38:27 Vova Exp $
 */

namespace SatCMS\Sat\Classes\Behaviors;

use core;

class BaseAttachs extends \model_behavior {

    /**
     * @var \model_collection
     */
    protected $attachs;

    // Options

    /** @var string attachID */
    protected $key = '';

    /** @var string model */
    protected $model_class = '';

    protected $deps = false;

    protected $with_ctype = true;

    function configure() {
        if (empty($this->key) || empty($this->model_class)) {
            throw new \collection_exception('Behavior ' . __CLASS__ . ' bad key/model');
        }
    }

    /**
     * Commit event
     * @param $data
     */
    function modify_after($data) {

        // ATTACH_SID only on new items
        $sid = @$data['attach_sid'];

        // otherwise use PID
        $pid = $data['id'];

        // core::selfie()->ajax_answer(array(__METHOD__,$data));

        // update pid if object need it
        if ($sid && method_exists($this->get_attach_model(), 'update_pid')) {
            $this->get_attach_model()->update_pid($sid, $pid);
        }

    }

    /**
     * Finish him
     */
    function remove_after() {
        // remove comments
        if ($attachs = $this->get_attachs())  $attachs->remove_all();
    }


    /**
     * Render key
     * @return string
     */
    function get_key() {
        return $this->key;
    }

    /**
     * load_secondary_after
     */
    function load_secondary_after($options) {

        if (($options && !is_array($options))
            || is_array($options) && in_array($this->get_key(), $options)) {

            $this->get_attachs();
        }
    }

    function render() {
        return $this->attachs->render();
    }

    /**
     * Render
     */
    function render_secondary_after() {

        if ($this->attachs) {
            $this->model->set_data($this->get_key(), $this->render());
        }
    }

    /**
     * Check w/dependencies
     * @return $this self:bool Return $this if set flag called (for chaining)
     */
    public function with_deps($fl = null) {
        if (!isset($fl)) return $this->deps;

        $this->deps = $fl;
        return $this;
    }

    /**
     * Get model
     * @return \model_collection
     */
    function get_attach_model() {
        return core::module('sat')->model($this->model_class);
    }

    /**
     * Load stuff
     * @return $this
     */
    function load_attachs() {

        $this->attachs = $this
            ->get_attach_model()
            ->set_where("pid = %d", $this->model->id)
            ->with_deps($this->deps)
        ;

        if ($this->with_ctype && ($ctype = $this->model->get_ctype_id())) {
            $this->attachs->where('ctype_id', $ctype);
        }

        $this->load_attachs_before();

        $this->attachs->load();

        $this->load_attachs_after();

        return $this->attachs;
    }

    function load_attachs_before() {}
    function load_attachs_after() {}

    /**
     * Get data
     * @return \model_collection
     */
    function get_attachs() {

        if (!isset($this->attachs)) {
            $this->load_attachs();
        }

        return $this->attachs;
    }



}