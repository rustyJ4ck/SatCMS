<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: widget_group.php,v 1.1.2.1 2013/01/30 06:53:30 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

use SatCMS\Modules\Sat\Editor\Controllers\SatController;

class sat_widget_group_controller extends SatController {
    
    protected $title = 'Виджеты (группы)';
     
    protected $_where = 'site_id = %d';
    
    protected $collection_config = array('with_module_prefix' => 1);
    
    protected $cmd_pid;
    
    function construct_after() {

        $this->_where = sprintf($this->_where
            , (int)$this->get_site_id()
        );
            
        // $this->base_url = $this->base_url;
              
    }

    // old stuff?
    function action_edit_after($_item) {
        
        if ($this->params->do == 'change_title') {
            $_item->title = functions::request_var('title');
            $_item->update_fields('title');
            if ($this->in_ajax()) $this->ajax_answer(true);
        }
        
        if ($this->params->do == 'change_url') {
            $_item->title = functions::request_var('url');
            $_item->update_fields('url');
            if ($this->in_ajax()) $this->ajax_answer(true);
        }
        
    }

}

