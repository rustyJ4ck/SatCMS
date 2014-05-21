<?php

/**
 * Texts Controller 
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: sat_text.php,v 1.1.4.2.2.2 2011/12/22 11:28:47 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

use SatCMS\Modules\Sat\Editor\Controllers\SatController;

class sat_text_controller extends SatController {

    protected $_req;
    
    protected $title = 'Тексты';
    
    protected $_where = 'site_id = %d';   
    
    function construct_after() {

        $this->params->pid = $this->get_site_id();

        $this->_where = sprintf($this->_where, $this->params->pid);
    }

    function action_modify_before() {
        $this->postdata['site_id'] = $this->params->pid;
    }
    
}