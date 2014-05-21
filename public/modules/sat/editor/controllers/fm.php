<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');

require "_req.php";     

class sat_fm_controller extends editor_controller {
    
    protected $collection = false;
    

    protected $_req;
    
    protected $title = 'Файловый менеджер';
    
    function construct_after() {
        
        $this->_req = new sat_controller_req();
        $this->set_template('fm');
    }
    
}