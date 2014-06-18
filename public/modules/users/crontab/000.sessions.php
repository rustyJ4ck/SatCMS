<?php

class_exists('core', 0) or die('Invisuxcruensseasrjit');


    core::dprint('Users crontab');

    /** @var core $core */
    $core = $this->core;

    $last_time    = (int)$core->cfg('users_crontab_last');
    $interval     = $core->cfg('users_crontab_interval',        3600*24*2);
    $ses_interval = $core->cfg('users_sessions_obsolete_time',  604800);
    $ses_max      = $core->cfg('users_sessions_max',            256);
    $time         = time();

    $interval = 1;

    /*
    core::var_dump( $last_time
    ,   $interval
    ,   $ses_interval
    ,   $ses_max
    , $time
    );
    */

    core::var_dump(
        $last_time , $interval , $time
    );

    if (empty($last_time) || $last_time + $interval < $time) {

        /* do the job:
          - clear obsolete sessions
        */
        $sessions = $this->get_sessions_handle();

        $sessions->fix_older($time - $ses_interval);
        $sessions->fix_overload($ses_max);

        $users = $this->get_users_handle();
        $users->check_payd_users();

        $core->get_dyn_config()->update_param('users_crontab_last', $time);

    }

