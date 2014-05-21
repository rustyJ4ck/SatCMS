<?php

/**
 * sqlite 3
 * PHP 5 >= 5.3.0
 * http://www.php.net/manual/en/book.sqlite3.php
 * 
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: sqlite3.php,v 1.1.2.4 2013/12/15 12:04:24 jack Exp $
 */
 

require_once "modules/core/dbal/dbal.php"; 
 
class sqlite3_db extends dbal {
    
    private $_db_file = '';
    
    /** @var SQLite3 */
    protected $_connect_id; 
    
    /* @var Sqlite3Result */
    protected $query_result;
    
    /**
    * @param array
    *   server = path to file
    */
    function connect() {   
    
        if (!class_exists('SQLite3', 0))
            throw new dbal_exception('Sqlite support not compiled with PHP!');   
            
        if (empty($this->dbname)) {
            throw new dbal_exception('Sqlite connect empty database!');   
        }
        
        // check is relative path (to site root)
        // $dbname = (substr($this->dbname, 0, 1) == '/')
        //    ? substr($this->dbname, 1)
        //    : ('../' . $this->dbname);
        
        $this->_db_file = loader::get_root($this->dbname);

        if (!file_exists($this->_db_file)) {
            throw new dbal_exception('No database: ' . $this->_db_file);
        }
        
        core::dprint(array('CONNECT %s %s', __CLASS__, $this->_db_file), core::E_SQL);
        
        $error = '';
        
        $this->_connect_id = new SQLite3(
            $this->_db_file, SQLITE3_OPEN_READWRITE /*| SQLITE3_OPEN_CREATE*/
        );
        
        if ($this->_connect_id)
        {
            $this->_connect_id->exec('PRAGMA short_column_names = 1');
            $this->_connect_id->exec('PRAGMA encoding = "UTF-8"');
        }
        else {
             throw new dbal_exception('Cant connect to database ' 
                . $this->_db_file                 
             );
        }
         
        return $this->_connect_id;   
    }
    
    /**
    * Query
    * 
    * @param mixed $query
    */
    function sql_query($query) {
        
        if (empty($query)) {
            core::dprint('Empty sql_query call');
            return false;
        }
        
        if (is_array($query)) {
            $query = vsprintf($query[0], array_slice($query, 1));
        }        
        
        $query = trim($query);
        
        if (empty($query)) return false;
        if (!$this->_connect()) return false;           
        
        ++$this->_counter;
        
        $this->_sqlite_fixes($query);
        
        $this->_last_query = $query;    
        
        $is_select = preg_match('@^(SELECT|PRAGMA)@i', $query);
       
        $microtime = microtime(1);
       
        // how the fuck to catch errors in query?
        $this->query_result = 
                $is_select 
                ? @$this->_connect_id->query($query) 
                : @$this->_connect_id->exec($query)
        ;
        
        $this->_last_query_time = microtime(1) - $microtime;
        
        core::dprint(array('[SQL%0d|%.5f : %s', $this->_counter, $this->_last_query_time, $query), core::E_SQL);

        if (!$this->query_result
           // || ($this->query_result instanceof SQLite3Result &&  $this->query_result->)
        )
        {
                $err = $this->sql_error();
                core::dprint('[SQL FAIL] ' . $err['message'] . ' : ' . $err['code'], core::E_SQL);
        }
        
        return $this->query_result;
    }

    function _sqlite_fixes(&$query) {
        
        $query = strings::replace(
            array('LOW_PRIORITY'),
            '',
            $query
        ); 
        
        // DELETE..LIMIT unsupported
        if (preg_match('@^DELETE@', $query)) {
            $query = preg_replace('@LIMIT \d+(,\s*\d+)?@', '', $query);
        }
        
    }
    
    /**
    * Fetch current row
    * @param Sqlite3Result
    */
    function sql_fetchrow($query_id = null)
    {             
        if (!isset($query_id)) {
            $query_id = $this->query_result;
        }
        
        $result = false;
                                 //SQLite3Result
        if ($query_id instanceOf SQLite3Result) {        
            $result = $query_id->fetchArray(SQLITE3_ASSOC);
        }
        else {
             core::dprint(array('[SQL FAIL] %s is not SQLite3Result is %s: %s'
                , __METHOD__, gettype($this->query_result), $this->_connect_id->lastErrorMsg()
                ), core::E_SQL);
        }
        
        return $result;

    }
    
    /**
    * Rowset
    * @param mixed $query_id
    */
    function sql_fetchrowset($query_id = null)
    {
        $result = array();

        if (!isset($query_id)) {
            $query_id = $this->query_result;
        }
        
        if ($query_id instanceof Sqlite3Result) {
            while($rowset = $query_id->fetchArray(SQLITE3_ASSOC)) {
                $result []= $rowset;
            }                                                             
        }
        else {
            $result = false;
            core::dprint(array('[SQL FAIL] %s is not Sqlite3Result', __METHOD__), core::E_SQL);
        }
        
        return $result;
    }   
    
    /**
    * Return number of affected rows
    */
    function sql_affectedrows() {
        return $this->is_connected() ? $this->_connect_id->changes() : false;
    }    
    
    /**
    * Get last inserted id after insert statement
    */
    function sql_nextid() {
        return $this->is_connected() ? $this->_connect_id->lastInsertRowID() : false;
    }
    
    /**
    * Escape string used in sql query
    */
    function sql_escape($msg) {
        return SQLite3::escapeString($msg);
    }    
    
    function sql_freeresult($query = 0) {
        if ($query) {
            $query->finalize();
        }
        return true;        
    }
    
    /**
    * Close sql connection
    * @access private
    */
    function sql_close() {
        $return = $this->is_connected() ? $this->_connect_id->close() : false;
        $this->_connect_id = $this->_connected = false;
        return $return;
    }    
    
    /**
    * return sql error array
    * @access private
    */
    function sql_error() {
        return array(
            'message'    => $this->_connect_id ? $this->_connect_id->lastErrorMsg() : 'not-connected',
            'code'       => $this->_connect_id ? $this->_connect_id->lastErrorCode() : 'not-connected'
        );
    }



    function get_tables($table = null) {

        $data = $this->fetch_all('SELECT * FROM sqlite_master WHERE type=\'table\''
            . ($table ? (' AND name LIKE \'' . $table . '\'') : '')
        );

        return $table
            ? (!empty($data) ? array_shift($data) : false)
            : $data;
    }

    function get_columns($table) {
        return $this->fetch_all('PRAGMA table_info(' . $table . ')');
    }

    function get_indexes($table) {
        return $this->fetch_all('PRAGMA index_info(' . $table . ')');
    }
    
    
}