<?php

/**
 * NullDB
 */
class null_db extends dbal {

    function sql_escape($data) {
        return $data;
    }

    function connect() {
        $this->_connected = true;
    }

    //
    // Other base methods
    //
    function sql_close() {
    }

    //
    // Base query method
    //
    function sql_query($query = "", $transaction = FALSE) {
        echo "SQL: " . $query;

        return true;
    }

    //
    // Other query methods
    //
    function sql_numrows($query_id = 0) {
        return false;
    }

    function sql_affectedrows() {
        return false;
    }

    function sql_fetchrow($query_id = 0) {
        return true;
    }

    function sql_fetchrowset($query_id = 0) {
        return true;
    }

    function sql_nextid() {
        return 1;
    }

    function sql_freeresult($query_id = 0) {
        return false;
    }

    function sql_error() {
        return true;
    }

    function free_result($qid = 0) {
        return true;
    }

    /**
     * @param null $table
     * @return array|bool
     */
    function get_tables($table = null) {
    }

    /**
     * @param $table
     * @return array|bool
     */
    function get_columns($table) {
    }

    function get_indexes($table) {
    }

} // class sql_db

