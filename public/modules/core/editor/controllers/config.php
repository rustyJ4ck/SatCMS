<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: config.php,v 1.2.6.3 2013/12/19 09:15:34 Vova Exp $
 */
  
class core_config_controller extends editor_controller {
    
    protected $title = 'Параметры';

    protected $_limit = 10;

    function run_before() {
    }
    
    function action_system() {      
        $this->collection->toggle_system($this->params->id, ('true' == functions::request_var('to', 'false')));        
        if ($this->in_ajax()) { $this->_ajax_answer(true, i18n::T('Status changed')); }            
    }   

}
