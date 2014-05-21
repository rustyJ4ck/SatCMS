<?php

/**
* mpbill
* 
* @package    SatCMS
* @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
* @copyright  SurSoft (C) 2008
* @version    $Id: sms_vip.php,v 1.2 2010/07/21 17:57:24 surg30n Exp $
*/
 
/** 
* /modules/users/money/sms_vip.php?user_id=71111111111&num=1121&msg=admin
* &skey=807140ca5ba73a2455029e952beae13a
* &operator_id=299&date=2008-10-23+17%3A01%3A50&smsid=1379554447&msg_trans=admin&operator=operator&test=1
*/

require '../../../modules/core/loader.php'; 
ini_set('dispaly_errors', 'off');

$core = core::get_instance(); 
                                                             
$core->lib('logger')->log('SMS Payment',  print_r($_GET, 1));

$smskey = $core->get_cfg_var('sms_seckey', '');

core::lib('renderer')->set_content_type('text/plain');   

$smsid      = functions::request_var('smsid', '');
$num        = functions::request_var('num', '');
$operator   = functions::request_var('operator', '');
$userid     = functions::request_var('user_id', '');
$cost       = functions::request_var('cost', '');
$msg        = functions::request_var('msg', '');
$skey       = functions::request_var('skey', '');   

list($prefix, $msg) = explode(' ', $msg);

if ($skey != md5($smskey)) {
    header("HTTP/1.0 404 Not Found");
    echo "Error! invalid sek key";
    die;
}

$user = trim($msg);
$user = core::module('users')->get_user($user, 'login');     

if ($user->is_anonymous()) {
    header("HTTP/1.0 404 Not Found");  
    echo "Error! invalid login";
    die;
}      

echo("smsid:$smsid\n");
echo("status:reply\n");
echo("content-type:text/plain\n\n");

$pay_for = $core->get_cfg_var('sms_payd_period', '30');
$user->pay_for($pay_for, $cost);

echo sprintf("User %s successfuly payd for %s days.", $user->login, $pay_for);

die;
 
