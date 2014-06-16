<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: group.php,v 1.1.2.1.4.2 2011/12/22 11:28:47 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

use SatCMS\Sat\Editor\Controllers\SatController;
  
class extrafs_group_controller extends SatController {
    
    protected $collection_config = array('with_module_prefix' => 1);
    
    protected $title = 'Группы';
    
    protected $_where = 'site_id = %d';
        
    function construct_after() {

        
        $this->_where = sprintf(
            $this->_where
            , $this->get_site_id()
        );         
    }
    
}

