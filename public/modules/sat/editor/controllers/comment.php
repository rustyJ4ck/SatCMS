<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

use SatCMS\Sat\Editor\Controllers\SatController;

class sat_comment_controller extends SatController {

    protected $title = 'Комментарии';
    
    protected $_limit = 20;
    protected $_order = 'id DESC';
    
    function construct_after() {

    }

    // $cdata = $cdata_filter->apply_filters()->get_collection()->render_parents();
    
}