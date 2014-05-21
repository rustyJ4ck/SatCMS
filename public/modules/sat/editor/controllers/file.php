<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

/**
 * Base class
 */
if (!class_exists('sat_node_image_controller', 0)) {
    require __DIR__ . '/node_image.php';
}

class sat_file_controller extends sat_node_image_controller {
    protected $title = 'Вложения - файлы';
}

