<?php

namespace SatCMS\Sat\Classes\Attachable;

use model_collection;
use aregistry;

/**
 * Used to attach child collections (generate sid for forms)
 * Class MasterCollection
 * @package SatCMS\Sat\Classes\Attachable
 */
class MasterCollection extends model_collection {

    /**
     * @var aregistry  array(abs_collection)
     */
    public $attachables;

    /**
     * Constructor
     */
    function construct_after() {
        parent::construct_after();
        $this->config->set('attachable.master', true);
        $this->attachables = new aregistry($this->attachables);
        $this->create_attachables();
    }

    /** @abstract create attachable model
     *
     * Create attachable collection, notify them on updates
     */
    function create_attachables() {

        foreach ($this->attachables as $key => $attach) {
            if (is_string($attach)) {
                $this->attachables->set($key, core::selfie()->model($attach));
            }
        }

        // Example:
        // $this->attachables->files = core::module('sat')->get_file_handle();
    }

    function make_attach_sid() {
        return mt_rand(1000, 1000000);
    }

    /*
    function load_for($pid, $ctype) {
        return $this->set_where('pid = %d AND ctype_id = %d', $pid, $ctype)->load();
    }

    function update_pid($sid, $pid) {
        $sql = "UPDATE %s SET sid = 0, pid = %d  WHERE sid = %d";
        $sql = sprintf($sql, $this->get_table(), $pid, $sid);
        $this->db->query($sql);

        return $this->db->sql_numrows();
    }
    */

}