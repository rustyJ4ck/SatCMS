<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

namespace SatCMS\Modules\Core\Base;

abstract class Runner {

    protected $config;

    function __construct($config = null) {
        $this->configure($config);
    }

    function configure($config) {
        $this->config = new \aregistry($config);
    }

    abstract function run();

}