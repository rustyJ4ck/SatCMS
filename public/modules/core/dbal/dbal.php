<?php

/**
 * DBAL interface
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: dbal.php,v 1.2.2.1.2.2 2013/12/17 12:12:23 Vova Exp $
 */
abstract class dbal {

    protected $_counter = 0;
    protected $_config;
    protected $_disabled_set_names = false;

    protected $charset = 'UTF8';
    protected $collation = "UTF8_general_ci";

    protected $_connected = false;
    protected $_connect_id = null;
    protected $query_result;

    // [config]
    protected $persistency;
    protected $user;
    protected $password;
    protected $server;
    protected $dbname;
    protected $prefix;
    protected $root;
    // [/config]

    protected $row = array();
    protected $rowset = array();
    protected $num_queries = 0;
    protected $in_transaction = 0;

    protected $sql_time = 0;

    protected $_debug_file = '/../tmp/sql.log.html';

    // cache
    protected $cache_path = '/cache/sql/';
    protected $cache_enabled;

    protected $_last_query = '';
    protected $_last_query_time = '';

    /**
     * Constructor
     */
    function __construct($config /*$sqlserver, $sqluser, $sqlpassword, $database, $prefix, $persistency = true*/) {
        $this->_config = $config;

        $this->persistency = @$config['persistency'];
        $this->user        = @$config['login'];
        $this->password    = @$config['password'];
        $this->server      = isset($config['server']) ? $config['server'] : 'localhost';
        $this->dbname      = @$config['database'];

        $this->prefix = @$config['prefix'];
        $this->root   = loader::get_public();

        if (is_callable(array($this, 'configure'))) {
            $this->configure($config);
        }

    }

    function __destruct() {
        $this->close();
    }

    function close() {
        return $this->sql_close();
    }

    abstract function sql_close();

    function get_config() {
        return $this->_config;
    }

    /**
     * @return string
     */
    function type() {
        return substr(get_class($this), 0, -3); // _db
    }

    /**
     * Check config
     */
    function check_config() {
        if (!isset($this->config['server'])) $this->config['server'] = '';
        if (!isset($this->config['database'])) throw new dbal_exception('Database is not set');

        return $this->config;
    }

    /**
     * Get prefix
     */
    function get_prefix() {
        return $this->prefix;
    }

    /**
     * @param $data
     * @return array
     * @throws dbal_exception
     */
    function escape($data) {

        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->escape($v);
            }
        } elseif (is_scalar($data)) {
            $data = $this->sql_escape($data);
        } else {
            throw new dbal_exception('Bad escape param');
        }

        return $data;
    }

    abstract function sql_escape($query);

    /**
     * Fetch all
     *
     * @param mixed $query
     * @param mixed $transaction
     */
    function fetch_all($query = null) {
        $return = $this->sql_query($query);

        return $this->sql_fetchrowset($return);
    }

    /**
     * @param $query
     * @return mixed query result
     */
    abstract function sql_query($query);

    abstract function sql_fetchrowset($qid = null);

    /**
     * Get last error
     */
    function get_error() {
        return $this->sql_error();
    }

    /**
     * Build LIMIT query
     * @return self
     */
    function sql_limit(&$query, $total, $offset = 0, $cache_ttl = 0) {
        // if $total is set to 0 we do not want to limit the number of rows
        if ($total == 0) {
            $total = -1;
        }

        $query .= "\n LIMIT " . ((!empty($offset)) ? $offset . ', ' . $total : $total);

        return $this;
    }

    function query($q) {
        return $this->sql_query($q);
    }

    function get_numrows() {
        return $this->sql_numrows();
    }

    /**
     * @deprecated use sql_affectedrows
     */
    function sql_numrows() {
        return $this->sql_affectedrows();
    }

    abstract function sql_affectedrows();

    function get_affectedrows() {
        return $this->sql_affectedrows();
    }

    function fetch_row($qid = null) {
        return $this->sql_fetchrow($qid);
    }

    abstract function sql_fetchrow($qid = null);

    function fetch_rowset($qid = null) {
        return $this->sql_fetchrowset($qip);
    }

    function get_next_id() {
        return $this->sql_nextid();
    }

    abstract function sql_nextid();

    function free_result() {
        return $this->sql_freeresult();
    }

    abstract function sql_freeresult();

    function get_last_query() {
        return $this->_last_query;
    }

    abstract function get_tables($table = null);

    abstract function get_columns($table);

    abstract function get_indexes($table);

    /**
     * delayed connect
     */
    protected function _connect() {
        if (!isset($this->_connect_id)) {
            $this->_connected = $this->connect();
        }

        return $this->is_connected();
    }

    /** @return bool connected */
    abstract function connect();

    function is_connected() {
        return $this->_connected;
    }

}
