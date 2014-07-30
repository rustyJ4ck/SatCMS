<?php

return array(

    'fields' => array(
        "id"                 => array('type' => 'numeric'),
        "serieid_fullname"   => array('type' => 'text', 'size' => 255, 'hidden' => true),
        "emitentid_fullname" => array('type' => 'text', 'size' => 255, 'c1lass' => 'fit'),

        "cardnumber"         => array('type' => 'text',
                                      'size' => 64,
                                      'sortable' => true,
                                      'editable' => true,
                                      'filter' => array('params' => array('BEGINS', 'AND'))
        ),

        "pincode"            => array('type' => 'numeric'),
        "embtext"            => array('type' => 'text', 'size' => 64),
        "rcarno"             => array('type' => 'text', 'size' => 64, 'editable' => true),
        "rname"              => array('type' => 'text', 'size' => 64),

        "bdate"              => array('type' => 'unixtime', 'format' => 'd.m.Y', 'sortable'     => true),

        "edate"              => array('type' => 'unixtime',
                                      'format' => 'd.m.Y',
                                      'filter'       => array('params' => array('BETWEEN', 'AND')),
                                      'sortable'     => true
        ),

        "statusid_fullname"  => array('type'        => 'boolean',
                                      'title'       => 'infcp\\statusid_fullname',
                                      'description' => 'DESC',
                                      'popover' => 'HELLO'
        ),

        "blocked"            => array('type' => 'boolean', 'title' => '@B', 'editable' => true),
        "empty"              => array('type' => 'boolean', 'title' => '@E', 'editable' => true),

        "user_id"            => array('type' => 'numeric', 'hidden' => true)
    )

);