<?php

/**
 * Core editor actions
 * 
 * @package    content editor actions
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: actions.php,v 1.2.2.2 2011/11/16 07:05:47 Vova Exp $
 */

return
array(
   'config'         => array( 'url' => '?m=core&c=config'    , 'title' => 'Переменные', 'default' => true)
 , 'logs'           => array( 'url' => '?m=core&c=logs', 'title' => 'logs')
 
 //, ''
 , 'mail_tpl'       => array( 'url' => '?m=core&c=mail_tpl',     'title' => 'Шаблоны писем')

 , 'feedback'       => array( 'url' => '?m=core&c=feedback', 'hidden' => true)

);

