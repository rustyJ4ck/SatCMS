<?php

/**
 * Flash upload controller 
 * 
 * @package    content
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: node_files_uploader.php,v 1.1.4.1.2.2 2011/12/22 11:28:47 Vova Exp $
 */
  
class_exists('core', 0) or die('Invisuxcruensseasrjit');

/*
return array(
      'id'          => array('type' => 'numeric')
    , 'pid'         => array('type' => 'numeric')      
    , 'title'       => array('type' => 'text', 'size' => 127)
    , 'created_at' => array('type' => 'unixtime', 'default' => 'now', 'autosave' => true)
    , 'file'        => array('type' => 'file', 'storage' => 'files',  'spacing'     => 3)
);
*/

if (empty($_FILES['files']) || empty($_FILES['files']['size'])) return;

$cmd_pid = core::lib('request')->get_ident('pid');

if (!$cmd_pid) throw new controller_exception('Bad pid');

$fh = $this->get_file_handle();

$f = $_FILES['files'];

$fh->create(array(
    'file'   => $f
    , 'pid'  => $cmd_pid
));
          
// core::get_instance()->ajax_answer($fh->get_db()->get_last_query());
core::get_instance()->ajax_answer($fh->get_last_item()->render());

/*
Array
(
    [files] => Array
        (
            [name] => 1.txt
            [type] => application/octet-stream
            [tmp_name] => Q:\xampp\xampplite\tmp\php14F.tmp
            [error] => 0
            [size] => 4
        )

)
*/