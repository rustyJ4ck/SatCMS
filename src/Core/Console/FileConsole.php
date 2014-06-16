<?php

/**
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */


namespace SatCMS\Core\Console;

class FileConsole {

    private $_file;
    private $_uid;

    private $_buffer = array();

    function __construct($params) {
        $file = 'console-' . (date(@$params['log'] ?: 'Y-m-d')) . '.log';
        $this->_file = \loader::get_temp($file);
        $this->_uid = substr(\functions::hash(microtime(true)), -5);
        $this->_head();
    }

    function __destruct() {
        $this->out('');
        $this->_flush();
    }

    function _flush() {
        file_put_contents($this->_file, join(PHP_EOL, $this->_buffer), FILE_APPEND);
    }

    private function _head() {
        $this->out('');
        $this->out('REQUEST #' . $this->_uid . ' ' . @$_SERVER['REQUEST_URI']);
        $this->out(str_repeat('-', 60));
        $this->out('');
    }

    function out($message, $group = null, $color = null) {
        $this->_buffer []= $message;
        // file_put_contents($this->_file, $this->_uid . '| ' . $message . PHP_EOL, FILE_APPEND);
    }

}