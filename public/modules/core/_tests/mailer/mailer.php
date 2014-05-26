<?php

require "../loader.php";    

/** @var mail_tpl_collection */
$tmail = core::get_instance()->get_mail_tpl_handle();

/** @var \tf\module\core\mail_tpl_item */
$item = $tmail->where('name', "feedback")->load_first();


test_assert($item instanceOf mail_tpl_item);

$to   = 'rustyj4ck@gmail.com';

$vars = array(
	'message' => 'blabla',
	'title'   => 'Hello from ' . __FILE__
);

test_assert($item->send($to, $vars));
