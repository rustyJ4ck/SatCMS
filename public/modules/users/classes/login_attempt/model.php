<?php

return array(

    'fields' => array(
           'id'        => array('type' => 'numeric')
        , 'uip'        => array('type' => 'numeric', 'unsigned' => true, 'long' => true)
        , 'login'      => array('type'     => 'text', 'size' => 127)
        , 'created_at' => array('type' => 'unixtime', 'default' => 'now')
    ),

    'config' => array(
        'schema' => array(
            'indexes' => array(
                'uip' => array('uip')
            )
        )
    )
);