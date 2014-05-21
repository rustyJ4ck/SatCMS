<?php

//wtf?
  
require('../loader.php');

$core = core::get_instance();

$last = core::module('users')->get_users_handle()
    ->set_where("id = %d", 1)
    ->set_limit(1)
    ->load();

printf("Count : %d \n", $last->count());

$i = $last->get_item();
printf ("1) %d = %s \n%s\n"
    , $i->get_id(), $i->email, print_r($i->data,1)
);

$i = $last->get_item(true);

if ($i) {

printf ("2) %d = %s \n"
    , $i->id, $i->email
);

}
else {
	echo "2) no items \n";
}