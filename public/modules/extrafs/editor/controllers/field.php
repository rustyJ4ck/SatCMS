<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: field.php,v 1.1.4.2.2.2 2011/12/22 11:28:47 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

use SatCMS\Modules\Sat\Editor\Controllers\SatController;
  
class extrafs_field_controller extends SatController {
    
    protected $collection_config = array('with_module_prefix' => 1);
    
    protected $title = 'Поля';
    
    protected $_where = 'gid = %d';

    function construct_after() {

        $this->_where = sprintf($this->_where
            , $this->params->gid
        );
    }
    
    function action_before() {
        
        $groups = $this->context->get_group_handle()
            ->set_where('site_id = %d', $this->get_site_id())
            ->load();
        
        if ($this->params->gid) {
            $group = $groups->get_item_by_id($this->params->gid);
            $group->set_data('current', true);
            $this->response->group = $group->render();
        }
        else {
            $this->disable_render(true);
        }
        
        // groups
        $this->response->groups = $groups->render();

        // field types
        $this->response->field_types = extrafs_field_collection::get_types();
    }
    
}

