<?php

/**
 * sqlite
 * 
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: sqlite.php,v 1.1.2.1 2013/10/16 11:47:12 Vova Exp $
 */
 

require_once "modules/core/dbal/dbal.php"; 
 
class sqlite_db extends dbal {
    
    private $_db_file = '';
    
    /**
    * @param array
    *   server = path to file
    */
    function connect() {
        
        core::dprint(array('CONNECT %s', $this->server), core::E_SQL);
        
        if (!function_exists('sqlite_open'))
            throw new dbal_exception('Sqlite support not compiled with PHP!');   
            
        if (empty($this->database)) {
            throw new dbal_exception('Sqlite connect empty database!');   
        }
        
        $this->_db_file = loader::get_root() . $this->database;
        
        $error = '';
        $this->_connect_id = ($this->persistency) 
            ? @sqlite_popen($this->_db_file, 0666, $error) 
            : @sqlite_open($this->_db_file, 0666, $error);

        if ($this->_connect_id)
        {
            @sqlite_query('PRAGMA short_column_names = 1', $this->_connect_id);
            //@sqlite_query('PRAGMA encoding = "UTF-8"', $this->db_connect_id);
        }
        else {
             throw new dbal_exception('Cant connect to database', $error);
        }
        

        return $this->_connect_id;

    }
    
    /**
    * Query
    * 
    * @param mixed $query
    */
    function sql_query($query) {
        
        if (empty($query)) return false;
        if (!$this->_connect()) return false;
       
        $this->_last_query = $query;          
       
        if (!($this->query_result = 
             (@sqlite_query($query, $this->_connect_id) === false)
        )) {
                $err = $this->sql_error();
                core::dprint('[SQL FAIL] ' . $query, core::E_SQL);
                core::dprint('[SQL FAIL] ' . $err['message'] . ' : ' . $err['code'], core::E_SQL);
        }
        
        return $this->query_result;
    }

    /**
    * Fetch current row
    */
    function sql_fetchrow($query_id = null)
    {
        if (!isset($query_id)) {
            $query_id = $this->query_result;
        }

        return @sqlite_fetch_array($query_id, SQLITE_ASSOC);
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

        if ($query_id) {
            while($rowset = sqlite_fetch_array($query_id, SQLITE_ASSOC)) {
                $result []= $rowset;
            }

        }
        else {
            $result = false;
        }
        
        return $result;
    }   
    
    /**
    * Return number of affected rows
    */
    function sql_affectedrows() {
        return ($this->is_connected()) ? @sqlite_changes($this->_connect_id) : false;
    }    
    
    /**
    * Get last inserted id after insert statement
    */
    function sql_nextid() {
        return ($this->is_connected()) ? @sqlite_last_insert_rowid($this->_connect_id) : false;
    }
    
    /**
    * Escape string used in sql query
    */
    function sql_escape($msg) {
        return @sqlite_escape_string($msg);
    }    
    
    function sql_freeresult($query_id = 0) {  
        return true;        
    }
    
    /**
    * Close sql connection
    * @access private
    */
    function sql_close() {
        $return = $this->_connect_id ? @sqlite_close($this->_connect_id) : false;
        $this->_connect_id = $this->_connected = false;
        return $return;
    }    
    
    /**
    * return sql error array
    * @access private
    */
    function sql_error() {
        return array(
            'message'    => @sqlite_error_string(@sqlite_last_error($this->_connect_id)),
            'code'       => @sqlite_last_error($this->_connect_id)
        );
    }
    
    
    
}