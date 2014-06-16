<?php

/**
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */


namespace SatCMS\Core\Console;

class FBConsole {

    function __construct($params) {
        ob_start(); // prevent headers sent
    }

    function out($message, $group, $color) {
        \fb::info($message, $group);
    }

}