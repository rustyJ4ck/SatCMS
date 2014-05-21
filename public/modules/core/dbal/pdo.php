<?php

/**
 * PDO adapter
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: pdo.php,v 1.2 2010/07/21 17:57:16 surg30n Exp $
 */

class pdo_db extends dbal { 
 
    /**
    * Construct
    */
    function __construct($config) {
        
        $this->config = $config;
        
        $config = $this->check_config();
        
        // if (empty($config['server'])) $config['server'] = 'localhost';
        
        if (!extension_loaded('pdo')) {
            throw new dbal_exception('The PDO extension is required');
        }

        if (!in_array($config['type'], PDO::getAvailableDrivers())) {           
            throw new dbal_exception("PDO {$config['type']} driver is not installed");
        }
        
        try {
            
            $dsn = $this->make_dsn();
            
            core::dprint($dsn);
            
            $this->db_connect_id = new PDO(
                $dsn,
                $this->config['login'],
                $this->config['password'],
                $this->config['driver_options']
            );

            // set the PDO connection to perform case-folding on array keys, or not
            // $this->db_connection_id->setAttribute(PDO::ATTR_CASE, $this->_caseFolding);

            // always use exceptions.
            $this->db_connect_id->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            throw new dbal_exception($e->getMessage());
        }
        
    
    }
    
    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function make_dsn()
    {           
        // $dns = $this->config['type'] . ':dbname=' . $this->config['database'] . ";host=" . $this->config['server']; 
        $dns = $this->config['type'] 
            . (!empty($this->config['server']) ? (':' . $this->config['server']) : '') 
            . ':' . $this->config['database'] 
            . (isset($this->config['driver_options']) ? (':' . $this->config['driver_options']) : ''); 
        return $dns;
    }
    
    function sql_numrows() {;}
    function sql_affectedrows() {;}
    function sql_fetchrow() {;}
    function sql_fetchrowset() {;}
    function sql_nextid() {;}
    function sql_close() {;}
    

}
  
