<?php
/**
 * Front entry point 
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: index.php,v 1.3.2.1 2010/08/07 22:50:40 surg30n Exp $
 */

require 'modules/core/loader.php';

loader::bootstrap(array(
    //loader::OPTION_TESTING => true,
    //loader::OPTION_DEBUG => 100,
    loader::OPTION_AUTORUN => true,
    loader::OPTION_CORE_PARAMS => array(
       // 'log_requests' => true
       // 'disable_database' => 1
       // 'debug' => 100
    )
));

 
