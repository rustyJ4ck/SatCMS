<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * $Id: mysql.php,v 1.8.2.3.2.6 2013/10/16 11:46:11 Vova Exp $
 */

require_once "modules/core/dbal/dbal.php";

class mysql_db extends dbal {
    function __construct($config /*$sqlserver, $sqluser, $sqlpassword, $database, $prefix, $persistency = true*/) {

        $this->_config = $config;

        $this->persistency = @$config['persistency'];
        $this->user        = $config['login'];
        $this->password    = $config['password'];
        $this->server      = isset($config['server']) ? $config['server'] : 'localhost';
        $this->dbname      = $config['database'];

        $this->prefix = $config['prefix'];
        $this->root   = loader::get_root();

        if (is_callable(array($this, 'configure'))) {
            $this->configure($config);
        }
    }

    function sql_escape($data) {
        return $this->_connected
            ? mysql_real_escape_string($data)
            : mysql_escape_string($data);
    }

    //
    // Constructor
    //

    /**
     * connect stuff
     */
    function connect() {

        if ($this->_connected) return;

        core::dprint(array('CONNECT %s', $this->dbname), core::E_SQL);

        $starttime = microtime(true);

        if (!function_exists('mysql_connect'))
            throw new dbal_exception('MySQL support not compiled with PHP!');

        if (empty($this->dbname)) throw new dbal_exception('Empty database given');

        $this->_connect_id = ($this->persistency)
            ? mysql_pconnect($this->server, $this->user, $this->password)
            : mysql_connect($this->server, $this->user, $this->password);

        if (empty($this->_connect_id)) throw new dbal_exception('Cant connect to database', $this->get_error());

        $dbselect = mysql_select_db($this->dbname);

        if (!$dbselect) throw new dbal_exception('Cant use database', $this->get_error());


        $endtime = microtime(true) - $starttime;

        $this->sql_time += $endtime;

        $this->_disabled_set_names = !empty($this->_config['disable_set_names']);

        $this->_connected = true;

        if (!empty($this->_config['fix_charset'])) {
            if ($this->_connect_id) $this->fix_charset();
        }

        $this->password = null;

        return $this->_connect_id;
    }

    //
    // Other base methods
    //

    function fix_charset() {

        if ($this->_disabled_set_names) return;

        // If not normalized

        $names = $this->charset;
        $this->query("SET NAMES '{$names}'");

        /*
        SET NAMES is equal:
        SET character_set_client = x;
        SET character_set_results = x;
        SET character_set_connection = x;
        */

        $collation_connection = $this->collation;
        if ($collation_connection) {
            $this->query("SET collation_connection='{$collation_connection}'");
        }
    }

    function sql_close() {
        $return    = false;
        $starttime = microtime(true);

        if ($this->_connect_id) {
            //
            // Commit any remaining transactions
            //
            if ($this->in_transaction) {
                mysql_query("COMMIT", $this->_connect_id);
            }

            $mtime   = microtime(true);
            $endtime = $mtime;

            $this->sql_time += $endtime - $starttime;

            $return = mysql_close($this->_connect_id);

            $this->_connect_id = null;
        }

        return $return;
    }

    //
    // Other query methods
    //

    function sql_numrows($query_id = null) {
        $starttime = microtime(true);

        if (!isset($query_id)) {
            $query_id = $this->query_result;
        }

        $endtime = microtime(true);

        $this->sql_time += $endtime - $starttime;

        return $query_id ? mysql_num_rows($query_id) : false;
    }

    function sql_affectedrows() {

        $starttime = microtime(true);

        $endtime = microtime(true);

        $this->sql_time += $endtime - $starttime;

        return ($this->_connect_id) ? mysql_affected_rows($this->_connect_id) : false;
    }

    function sql_numfields($query_id = null) {
        $starttime = microtime(true);

        if (!isset($query_id)) {
            $query_id = $this->query_result;
        }

        $endtime = microtime(true);

        $this->sql_time += $endtime - $starttime;

        return ($query_id) ? mysql_num_fields($query_id) : false;
    }

    function sql_fieldname($offset, $query_id = null) {
        $starttime = microtime(true);

        if (!isset($query_id)) {
            $query_id = $this->query_result;
        }

        $endtime = microtime(true);

        $this->sql_time += $endtime - $starttime;

        return ($query_id) ? mysql_field_name($query_id, $offset) : false;
    }

    function sql_fieldtype($offset, $query_id = null) {
        $starttime = microtime(true);

        if (!isset($query_id)) {
            $query_id = $this->query_result;
        }

        $endtime = microtime(true);

        $this->sql_time += $endtime - $starttime;

        return ($query_id) ? mysql_field_type($query_id, $offset) : false;
    }


    function fetch_row($qid = 0) {
        return $this->sql_fetchrow($qid);
    }

    function sql_fetchrow($query_id = 0) {
        $starttime = microtime(true);

        if (!$query_id) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            $row     = mysql_fetch_array($query_id, MYSQL_ASSOC);
            $endtime = microtime(true);
            $this->sql_time += $endtime - $starttime;

            return $row;
        } else {
            $endtime = microtime(true);
            $this->sql_time += $endtime - $starttime;

            return false;
        }
    }

    function sql_fetchrowset($query_id = 0) {
        $result = array();

        $starttime = microtime(true);

        if (!$query_id) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            $iquery_id = intval($query_id);

            while ($rowset = mysql_fetch_array($query_id, MYSQL_ASSOC)) {
                $result[] = $rowset;
            }

            $endtime = microtime(true);
            $this->sql_time += $endtime - $starttime;

            //$this->free_result($iquery_id);

            return $result;
        } else {
            $endtime = microtime(true);
            $this->sql_time += $endtime - $starttime;
        }

        return false;
    }

    /**
     * Fetch field
     */
    function fetch_field($field, $rownum = -1, $query_id = 0) {
        return $this->sql_fetchfield($field, $rownum, $query_id);
    }

    function sql_fetchfield($field, $rownum = -1, $query_id = 0) {
        $starttime = microtime(true);

        if (!$query_id) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            if ($rownum > -1) {
                $result = mysql_result($query_id, $rownum, $field);
            } else {
                if (empty($this->row[$query_id]) && empty($this->rowset[$query_id])) {
                    if ($this->sql_fetchrow()) {
                        $result = $this->row[$query_id][$field];
                    }
                } else {
                    if ($this->rowset[$query_id]) {
                        $result = $this->rowset[$query_id][0][$field];
                    } else if ($this->row[$query_id]) {
                        $result = $this->row[$query_id][$field];
                    }
                }
            }

            $endtime = microtime(true);

            $this->sql_time += $endtime - $starttime;

            return $result;
        } else {
            $endtime = microtime(true);

            $this->sql_time += $endtime - $starttime;

            return false;
        }
    }

    function sql_rowseek($rownum, $query_id = 0) {
        $starttime = microtime(true);

        if (!$query_id) {
            $query_id = $this->query_result;
        }

        $endtime = microtime(true);

        $this->sql_time += $endtime - $starttime;

        return ($query_id) ? mysql_data_seek($query_id, $rownum) : false;
    }

    function sql_nextid() {
        $this->_connect();

        return ($this->_connect_id) ? mysql_insert_id($this->_connect_id) : false;
    }

    function sql_freeresult($query_id = null) {

        $starttime = microtime(true);

        if (!isset($query_id)) {
            $query_id = $this->query_result;
        }

        if (is_resource($query_id)) {
            unset($this->row[$query_id]);
            unset($this->rowset[$query_id]);

            mysql_free_result($query_id);

            $endtime = microtime(true);

            $this->sql_time += $endtime - $starttime;

            return true;
        } else {
            $endtime = microtime(true);

            core::dprint(__METHOD__ . ' with invalid resource', core::E_SQL);

            $this->sql_time += $endtime - $starttime;

            return false;
        }
    }

    function get_version() {
        $version = $this->sql_fetchrow($this->sql_query("SELECT VERSION() as version"));

        return $version['version'];
    }

    /**
     * SQL query
     *
     * @param mixed $query
     * @param mixed $transaction
     */
    function sql_query($query, $options = null) {

        if (empty($query)) {
            core::dprint('Empty sql_query call');

            return false;
        }

        if (is_array($query)) {
            $query = vsprintf($query[0], array_slice($query, 1));
        }

        $starttime = microtime(true);

        ++$this->_counter;

        $tm_ = core::get_instance()->time_check('mysql', 1, 1);

        unset($this->query_result);

        $this->num_queries++;

        $this->_connect();

        if (!$this->_connect_id && class_exists('core', 0)) {
            core::dprint(array('[SQL_ERROR] no connect %s', $query), core::E_SQL);
        }

        if (!empty($options['mysql_unbuffered'])) {
            $this->query_result = mysql_unbuffered_query($query, $this->_connect_id);
        } else {
            $this->query_result = mysql_query($query, $this->_connect_id);
        }

        $this->_last_query = $query;

        if ($this->query_result) {

            $endtime = microtime(true);

            $this->sql_time += $endtime - $starttime;

            if (core::get_instance()->cfg('debug_sql') >= 100) {
                core::dprint('<b>' . ($this->_counter) . ' ' . $this->num_queries . ')  ' . htmlspecialchars($query) . ' [' . round($this->sql_time, 4) . 'ms]</b>');

                // debug code idea by jovani (phpbbguru.net/community/profile.php?mode=viewprofile&u=12)
                if (strtoupper(substr($query, 0, 6)) == "SELECT") {

                    $x = mysql_query("EXPLAIN $query", $this->_connect_id);
                    $z = array();

                    while ($y = mysql_fetch_array($x, MYSQL_ASSOC)) {
                        core::dprint("&nbsp;&nbsp;&raquo; tbl = " . $y['table'] . " type = " . $y['type'] . " possible = " . $y['possible_keys'] . " used = " . $y['key'] . " len = " . $y['key_len'] . " ref = " . $y['ref'] . " rows = " . $y['rows'] . " extra = " . $y['Extra']);
                    }

                    mysql_free_result($x);
                }
            }


            // replace password in query for log
            $query = preg_replace("#password = '.*'#U", "password = '*******'", $query);

            if (core::get_instance()->get_cfg_var('debug_sql')) {
                $tm_ = core::get_instance()->time_check('mysql', 1);

                core::dprint($this->_counter . '. ' . $query . ' --[time : ' . $tm_ . ' s]', core::E_SQL);
                if (0 && ($console = core::lib('console'))) {
                    $dbg_trace = $console->debug_backtrace_smart();
                    if (isset($dbg_trace[2]) && !isset($dbg_trace[2]['class'])) $dbg_trace[2]['class'] = '';
                    $dbg_info = $dbg_trace[1]['file'] . ' in ' . $dbg_trace[1]['line'] . (!isset($dbg_trace[2]) ? '' : " ({$dbg_trace[2]['class']}::{$dbg_trace[2]['function']}) ");
                    core::dprint(' -- in ' . $dbg_info);
                }

            }
        } else {

            $endtime = microtime(true);

            $this->sql_time += $endtime - $starttime;

            if (core::get_instance()->get_cfg_var('debug_sql')) {
                $tm_ = core::get_instance()->time_check('mysql', 1);
                $err = $this->sql_error();
                core::dprint('[* SQL FAIL] ' . $query, core::E_SQL);
                core::dprint('[* SQL FAIL] ' . $err['message'] . ' : ' . $err['code'] . ' --[time : ' . $tm_ . ' s]', core::E_SQL);
            }
        }

        return $this->query_result;
    }

    function sql_error() {
        $starttime = microtime(true);

        $result['message'] = mysql_error($this->_connect_id);
        $result['code']    = mysql_errno($this->_connect_id);

        $endtime = microtime(true);

        $this->sql_time += $endtime - $starttime;

        return $result;
    }

    /**
     * @param null $table
     * @return array|bool
     */
    function get_tables($table = null) {

        $data = $this->fetch_all('SHOW TABLES'
            . ($table ? (' LIKE \'' . $table . '\'') : '')
        );

        return $data;
    }

    /**
     * @param $table
     * @return array|bool
     */
    function get_columns($table) {

        return $this->fetch_all('SHOW COLUMNS FROM ' . $table);

    }

    function get_indexes($table) {
        return $this->fetch_all('SHOW INDEX FROM ' . $table);
    }

}

