<?php
/**
 * Entry point for editor interface
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: index.php,v 1.3.2.2.4.6 2012/10/25 09:57:45 Vova Exp $
 */

// @fixme multifileuploader ua check fix
if (!empty($_SERVER['HTTP_USER_AGENT']) &&
    $_SERVER['HTTP_USER_AGENT'] == 'Shockwave Flash' && isset($_POST['_ua'])) {
    $_SERVER['HTTP_USER_AGENT'] = $_POST['_ua'];        
    $_COOKIE['vidz0xoid']       = $_POST['_sid'];
    $_REQUEST['with_ajax']      = 1;  
}

require '../modules/core/loader.php';

loader::bootstrap(array(
    loader::OPTION_CORE_PARAMS => array(
        'editor' => true
    )
));

// disable cache
functions::headers_no_cache();

/** @var tf_editor $editor */
$editor = core::lib('editor');

if (!core::lib('auth')->logged_in()) {
    core::dprint('Please login!');
    $editor->on_exception('Not logged in');
    functions::redirect('/editor/in/');
    return;
} 

$core = core::selfie();

/** @var user_item */
$user = core::lib('auth')->get_user();

$path = @$_GET['req'];

// allow ng-redirect
if (strpos($core->request->uri(), '/editor/redirect') === 0) {
   // die('ng-redirect');
   $path = '/editor/core/redirect/';
}

try {
    core::module('users')->check_forged();
}
catch (controller_exception $e) {
    $editor->on_exception($e->getMessage(), $e);
}

// parse request path
$editor->dispatch($path, core::get_params());

$module = core::get_params()->m;
$module = $module ? $module : 'core';

$is_main = $core->request->uri() == '/editor/' ? 1 : 0;

$mod_level = $user->get_container()->get_level_by_name('mod');

// so userish
if ($user->level < $mod_level) {
    $core->ajax_answer('Not allowed', 1);
}
// basic user levels filter
elseif (!$is_main && $user->level == $mod_level && 'sat' != $module && $user->level < core::module($module)->cfg('editor.level', 100)) {
    $editor->on_exception('MOD: Нет доступа к разделу');
}
// acls
elseif (!$is_main && !$user->is_allow('mod_' . $module)) {
    $editor->on_exception('Нет доступа к разделу');
}
else {
    try {
        if ($mod = core::module($module)) {
            $mod->on_editor();            
        }    
    }
    catch (module_exception $e) {
        // module not found        
        $core->error404('Module not found: ' . $e->getMessage());
    }
    catch (acl_exception $e) {
        $editor->on_exception($e->getMessage());
    }
    catch (controller_exception $e) {
        $editor->on_exception($e->getMessage());
    }

}

// we done here
$core->shutdown();