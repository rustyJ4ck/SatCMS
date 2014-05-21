<?php

/**
 * Front entry point (gate for cache mods)
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: gate.php,v 1.1.2.4 2011/05/10 17:56:45 surg30n Exp $
 */

$uri  = $_SERVER['REQUEST_URI'];
$host = $_SERVER['HTTP_HOST'];
$root = str_replace('\\', '/', dirname(__FILE__)) . '/';

$match = $root . 'static/' . $host . $uri;       
     
if (preg_match('@[^a-zA-Zа-яА-Я\d_\-\.\,\/]@u', $uri) || preg_match('@\.\.@', $uri)) {
    header(' ', true, 404);
    die('Bad uri');
}


$matched = false;

if (preg_match('@\/$@', $match)) {
    // is_dir fail with trailing slash?
    // this code will fail with junctions on windows
    $match_index = $match . 'index.html';
    $match = preg_replace('@\/$@', '', $match);
    if (is_dir($match) && file_exists($match_index)) 
    $matched = $match_index;
}
else {
    if (file_exists($match)) {
        $matched = $match;
    }
}
  
if ($matched) {
    
    $config = parse_ini_file('../config/engine.cfg');
    $config_host_ = '../config/' . $host . '.engine.cfg';
    if (file_exists($config_host_)) {
        $config_host = parse_ini_file($config_host_);
        $config = array_merge($config, $config_host);
    }
    
    $last_mod = filemtime($matched);
    
    $last_modified = date('r', $last_mod);
    $etag = md5($last_mod . $matched);

    // Not Modified
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH']))) {
        if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $last_modified || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $etag) {
            header('HTTP/1.1 304 Not Modified');
            exit();
        }
    }

    // Last-Modified    Tue, 26 Apr 2011 05:14:17 GMT    
    @header('ETag: "' . $etag . '"');
    @header('Last-Modified: ' . $last_modified);
    @header('Cache-Control: max-age=3600, must-revalidate');
    // readfile($matched);

    $buffer = file_get_contents($matched);
   
    if (!empty($config['gatemod'])) {
        $mods = explode(',', $config['gatemod']);
        foreach ($mods as $m) {
            $m = trim($m);
            require "gatemod/{$m}.php";
            $gclass = "{$m}_gatemod";
            $g = new $gclass ($config);
            $g->run($buffer);            
        }
    }
    
    echo $buffer;
    echo "\n<!--WITH_GATEMOD-->";

}
else {
    @header(' ', true, 404);
    die('Bad gate');
}
 