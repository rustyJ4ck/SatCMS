<?php
/**
 * SatCMS  http://satcms.ru/
 * @author Golovkin Vladimir <rustyj4ck@gmail.com> http://www.skillz.ru
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

 
