#!/usr/local/bin/php
<?php

/**
 * Cron entry point
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: cron.php,v 1.2 2010/07/21 17:57:14 surg30n Exp $
 */

// run crontab on single module

if (empty($_SERVER['argv'])) {
    die('Crontab direct access disabled');
}

set_time_limit(0);

require 'modules/core/loader.php';

loader::bootstrap(array(
    loader::OPTION_CRONJOB     => true,
    loader::OPTION_CORE_PARAMS => array(
        'debug' => 666
    )
));

// force debug 
core::set_debug(666);
ini_set('display_errors', 'on');
error_reporting(E_ALL);
//

$module = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : false;
loader::core()->crontab($module);

// done

 
