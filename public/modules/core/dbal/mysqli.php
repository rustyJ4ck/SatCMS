<?php

/**
 * PDO adapter
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: mysqli.php,v 1.2.6.1 2012/05/17 08:58:18 Vova Exp $
 */

class mysqli_db extends dbal { 
 
    /**
    * Construct
    */
    function __construct($config) {
        
        $this->config = $config;        
        $this->check_config();
        
        if (!extension_loaded('mysqli')) {
            throw new dbal_exception('The Mysqli extension is required.');
        }
        
        $port = (isset($this->config['port'])) ? (int)$this->_config['port'] : null;
                
        @$this->connection = new mysqli(
            $this->config['server'],
            $this->config['login'],
            $this->config['password'],
            $this->config['database'],
            $port
        );
        
        if ($this->connection === false || mysqli_connect_errno()) {
            throw new dbal_exception(mysqli_connect_error());
        } 
    }
    
    function sql_query($query) {
        $this->result = $this->connection->query($query);
        return $this->result;
    }
    
    function sql_numrows() {
        return $this->result->numrows();
    }
    
    function sql_affectedrows() {
         return $this->affected_rows();  
    }
    
    function sql_fetchrow() {
        return $this->result->fetch_assoc();
    }
    
    function sql_fetchrowset() {
        $data = array();
        while ($row = $this->result->fetch_assoc()) $data[] = $row;
        $this->sql_freeresult();
        return $data;
    }
    
    function sql_freeresult() {
        $this->result->free();
    }
    
    function sql_nextid() {
        $this->connection->insert_id;
    }
    
    function sql_close() {
        $this->connection->close();
        $this->connection = null;
    }
    
    function get_version() {
        $version = $this->sql_fetchrow($this->sql_query("SELECT VERSION() as version"));
        return $version['version'];
    }    

}
  
