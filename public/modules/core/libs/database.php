<?php
/**
 * Database loader
 * This is not core lib, use it by db_loader::
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: database.php,v 1.3 2010/07/21 17:57:17 surg30n Exp $     
 */
 
 class db_loader {
     
     private static $dbs;
     
     /**
     * Create a database handle
     * Factory method
     * @param array params
     */
     public static function get(array $config) {   
         
         $engine = $config['engine'];         
         
         if (isset(self::$dbs[$engine])) return self::$dbs[$engine];
         
         $engine_script =  loader::get_public(loader::DIR_MODULES) . 'core/dbal/' . $engine . loader::DOT_PHP;
         
         core::dprint('[db] ' . $engine_script);
         fs::req($engine_script, true);
         
         if (!isset($config['server'])) $config['server'] = 'localhost';
         
         // create instance
         $class = "{$engine}_db";         
                  
         try {
             return (self::$dbs[$engine] = new $class (
                  $config['server']
                , $config['login']
                , $config['password']
                , $config['database']
                , $config['prefix']
             ));
         }
         catch (dbal_exception $e) {
             return false;
         }

     }
     
 }