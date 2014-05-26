<?php

namespace SatCMS\Modules\Sat\Classes\Attachable;

use abs_collection;

/**
 * 'sid'         => array('type' => 'numeric', 'unsigned' => true)
 * 'ctype_id'    => array('type' => 'numeric')
 */
class SlaveCollection extends abs_collection {

    function construct_after() {
        parent::construct_after();
        $this->config->set('attachable.slave', true);
    }

    function load_for($pid, $ctype) {
        return $this->set_where('pid = %d AND ctype_id = %d', $pid, $ctype)->load();
    }

    /**
     * Rebind children sid -> pid after save parent model
     * @param $sid
     * @param $pid
     * @return mixed
     */
    function update_pid($sid, $pid) {
        $sql = "UPDATE %s SET sid = 0, pid = %d  WHERE sid = %d"; //@todo AND ctype_id = %d
        $sql = sprintf($sql, $this->get_table(), $pid, $sid);
        $this->db->query($sql);
        return $this->db->sql_numrows();
    }

}