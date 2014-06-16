<?php
/**
 * Database loader
 * This is not core lib, use it by db_loader::
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: db_loader.php,v 1.2 2010/07/21 17:57:17 surg30n Exp $
 */

require_once 'modules/core/dbal/dbal.php';

class db_loader {

    const DEFAULT_CONNECTION = 'default';
    const MOCK_CONNECTION    = 'null';

    private static $dbs;

    /**
     * Create a database handle
     * Factory method
     * @param array params
     *  engine - pdo
     *      type - mysql
     *
     * @return \Doctrine\DBAL\Connection
     */
    public static function get_doctrine($id = self::DEFAULT_CONNECTION, array $config = array()) {

        if (empty($config) && !isset(self::$dbs[$id])) {
            throw new dbal_exception('Try to get unloaded db connection : ' . $id);
        }

        $engine = @$config['engine'] ? : 'pdo_mysql';

        if (isset(self::$dbs[$id])) return self::$dbs[$id];

        core::dprint('[dbloader|doctrine::get] ' . $id . '(' . $engine . ')');

        if ($engine == 'null') {
            if (!class_exists('null_db', 0)) {
                require "modules/core/dbal/null.php";
            }

            $conn           = new null_db();
            self::$dbs[$id] = $conn;
        } else {
            $d_config = new \Doctrine\DBAL\Configuration();
            $d_config->setSQLLogger(new \SatCMS\Core\Dbal\Doctrine\Logger);

            /*
             *    'dbname'    => @$config['database']
                , 'user'      => @$config['login'] ?: 'root'
                , 'password'  => @$config['password']
                , 'host'      => @$config['server'] ?: 'localhost'
                , 'driver'    => $engine
                , 'path'      => (isset($config['path']) ? loader::get_root($config['path']) : null)
             */

            $connection_params = array(
                'driver' => $engine
            , 'prefix'   => @$config['prefix']
            , 'charset'  => 'UTF8'
            );

            unset($config['engine']);

            // fix path
            if (isset($config['path'])) {
                $config['path'] = loader::get_root($config['path']);
            }

            // merge params
            $connection_params = array_merge($connection_params, $config);

            core::dprint_r($connection_params);

            try {
                $conn           = \Doctrine\DBAL\DriverManager::getConnection($connection_params, $d_config);
                self::$dbs[$id] = $conn;
            } catch (Exception $e) {
                core::dprint($e->getMessage());

                return false;
            }
        }

        return self::$dbs[$id];
    }


    /**
     * Create a database handle
     * Factory method
     * @param array params
     *  engine - pdo
     *      type - mysql
     */
    public static function get($key = null, array $config = array()) {

        if (!isset($key)) $key = self::DEFAULT_CONNECTION; // $engine . $type;

        if (isset(self::$dbs[$key])) return self::$dbs[$key];

        return false;
    }

    /**
     * @param null $key
     * @param array $config
     * @return bool
     */
    public static function set($key = null, array $config = array()) {

        if (!isset($key)) $key = self::DEFAULT_CONNECTION; // $engine . $type;

        $engine = $config['engine'];
        $type   = @$config['type'];

        $engine_script = loader::DIR_MODULES . 'core/dbal/' . $engine . loader::DOT_PHP;

        core::dprint('[db] ' . $engine_script);
        fs::req($engine_script, true);

        // create instance
        $class = "{$engine}_db";

        try {
            return (self::$dbs[$key] = new $class (
                $config /*
                  $config['server']
                , $config['login']
                , $config['password']
                , $config['database']
                , $config['prefix'] */
            ));
        } catch (dbal_exception $e) {
            core::dprint($e->getMessage());

            return false;
        }
    }

}