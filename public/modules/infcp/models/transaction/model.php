<?php

/*
"rowno": "0",
"id": "1561329",
"fuelstationid_fullname": "Рм ~Second, Офис",
"opdate": "2014.05.22 16:30:42",
"cardno": "1001000028150017",
"carnumber": "",
"ownername": "ТЕСТОВАЯ",
"checkno": "10",
"productid_fullname": "AdBlue",
"amount": "-2",
"price": "350",
"currencyid_fullname": "PLN"
 */


return array(

    'fields' => array(
        "id"                 => array('type' => 'numeric'),
        "rowno"              => array('type' => 'numeric'),

        "fuelstationid_fullname" => array('type' => 'text', 'size' => 255),
        "cardno"             => array('type' => 'text', 'size' => 64, 'sortable' => true),

        "carnumber"          => array('type' => 'text', 'size' => 64),
        "ownername"          => array('type' => 'text', 'size' => 64),
        "checkno"            => array('type' => 'text', 'size' => 64),

        "productid_fullname" => array('type' => 'unixtime', 'format' => 'd.m.Y'),
        "edate"              => array('type' => 'unixtime', 'format' => 'd.m.Y'),

        "amount"              => array('type' => 'numeric'),
        "price"               => array('type' => 'numeric', 'float' => true),
        "currencyid_fullname" => array('type' => 'text', 'size' => 64),

        "user_id"             => array('type' => 'numeric')
    )

);