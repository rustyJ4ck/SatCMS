<?php

/**
 * @package core
 * @version $Id: modules.php,v 1.8.2.1.4.4 2012/09/10 05:59:21 Vova Exp $
 * @copyright (c) 2007 4style
 * @author surgeon <r00t@skillz.ru>
 */


/**
 *  Core libs registry
 */
class core_libs extends registry {

    private $configs  = array();
    private $resolved = array();

    // deffered config

    function configure($id, $cfg) {
        $this->configs[$id] = $cfg;
        return $this;
    }

    /**
     * @param string $id
     * @param core_libs $this
     */
    function set($id, $lib = null) {

        if ($lib && is_object($lib) && !($lib instanceof Closure)) {
            $this->_resolved($id, $lib);
        }

        return parent::set($id, $lib);
    }

    function is_resolved($id) {
        return in_array($id, $this->resolved);
    }

    function _get($id) {
        return parent::get($id);
    }

    /**
     * Get library (singleton)
     * @param string $id
     * @return mixed
     */

    function get($id) {

        $lib = $this->_get($id);

        // resolve
        if (!$this->is_resolved($id)) {

            if ($lib instanceof Closure) {
                $lib = $lib();
                $this->set($id, $lib);
            }

            $this->_resolved($id, $lib);
        }

        return $lib;

    }

    private function _resolved($id, $lib) {

        $this->resolved []= $id;

        // Closures support, resolve once
        if (!empty($this->configs[$id]) && method_exists($lib, 'configure')) {
            $lib->configure($this->configs[$id]);
        }

    }
}