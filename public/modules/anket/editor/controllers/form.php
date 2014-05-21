<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: form.php,v 1.1.2.1.2.2 2011/12/22 11:28:43 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

use SatCMS\Modules\Sat\Editor\Controllers\SatController;
  
class anket_form_controller extends SatController {
    
    protected $collection_config = array('with_module_prefix' => 1);
    
    protected $title = 'Анкета';
    
    protected $_where = 'site_id = %d';
        
    function construct_before() {
        
        $this->_where = sprintf($this->_where, $this->get_site_id());
        
        if (!$this->get_site_id()) throw new controller_exception('Empty site_id');
    }
    
}

