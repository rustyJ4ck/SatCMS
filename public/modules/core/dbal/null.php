<?php

/**
* Null
*/

class null_db extends singleton
{
   
    var $db_connect_id;
    var $query_result;
    var $row = array();
    var $rowset = array();
    var $num_queries = 0;
    var $in_transaction = 0;
    var $sql_time = 0; // SQL excution time

       
    function sql_escape($data) {
        return $data;
    }         
    
    function escape($data) {
        return $this->sql_escape($data);
    }        
   
    //
    // Other base methods
    //
    function sql_close()
    {          
    }

    //
    // Base query method
    //
    function sql_query($query = "", $transaction = FALSE)
    {     
        echo "SQL: " . $query;
        return true;      
    }
    
    function get_prefix()   { return null; }
    function connect()      { return null; }
    function close()        { return null; }    
    function query()        { return null; }
    function fetch_row()    { return null; }
    function fetch_rowset() { return null; }
    function nextid()       { return null; }
    function error()        { return null; } 

    //
    // Other query methods
    //
    function sql_numrows($query_id = 0)
    { 
        return false;
    }

    function sql_affectedrows()
    {
        return false;
    }    

    function sql_fetchrow($query_id = 0)
    {                  
            return true;                      
    }

    function sql_fetchrowset($query_id = 0)
    {          
            return true;
    }

    function sql_nextid()
    {  
        return 1;
    }

    function sql_freeresult($query_id = 0)
    {
       return false;      
    }

    function sql_error()
    {         
       return true;
    }

    function free_result($qid = 0) {
        return true;
    }

} // class sql_db

