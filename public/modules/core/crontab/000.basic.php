<?php

/**
 * core crontab 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: 000.basic.php,v 1.2 2010/07/21 17:57:21 surg30n Exp $
 */

class_exists('core', 0) or die('Invisuxcruensseasrjit');

core::dprint('[CRONTAB] core');

$last_time    = $this->cfg('core_crontab_last',            0);
$interval     = $this->cfg('core_crontab_interval',        600);
$bans_max     = $this->cfg('bans_max',                     256);
              
$time         = time();

$this->get_dyn_config()->update_param('core_crontab_last', $time);

if (empty($last_time) || $last_time + $interval < $time) {

    $bans = $this->get_bans_handle();
    $bans->fix_older($time - $interval);
    $bans->fix_overload($bans_max);   
}

// logs (per day)

$last_time    = $this->cfg('core_logs_rotate_last',            0);
$interval     = $this->cfg('core_logs_rotate_interval',        86400);
$logs_max     = $this->cfg('core_logs_max',                    256);
$logs_days    = $this->cfg('core_logs_days',                   30);
    
if (empty($last_time) || $last_time + $interval < $time) {        
    
    $logs = $this->get_logs_handle();
    $logs->fix_older($logs_days); 
    $logs->fix_overload($logs_max);
    
    $this->get_dyn_config()->update_param('core_logs_rotate_last', $time); 
}

  
