<?php

require "../loader.php";

/** @var sessions_collection $s */
$s = core::module('users')->get_sessions_handle();

/*
 * uip,uid,last_update,skey,sid,sdata) VALUES
 * (3285203570,0,1400497136,'e19766aad5b5e70c941abbe03d7a29ba','9756894d70bfebbde8c2b1fa9ee15fb8','');
 */

$id = $s->create([
      'uip'   => 3285203570
    , 'skey'  => 'e19766aad5b5e70c941abbe03d7a29ba' // generated
    , 'sid'   => '9756894d70bfebbde8c2b1fa9ee15fb8'
]);

test_assert($id && ($count = $s->count_sql()), 'x'.$count);

/** @var sessions_item $session */
$session = $s->load_only_id($id);

test_assert($session->uip  == 3285203570, '3285203570 <> ' . $session->uip . ' : ' . $session->uip_string);
// test_assert($session->skey === 'e19766aad5b5e70c941abbe03d7a29ba', 'e19766aad5b5e70c941abbe03d7a29ba <> ' . $session->skey);
test_assert($session->sid  === '9756894d70bfebbde8c2b1fa9ee15fb8', $session->sid);

dd(
    $session->as_array()
);
