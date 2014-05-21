<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: users.php,v 1.3.6.2 2011/12/22 11:28:49 Vova Exp $
 */
  
class users_users_controller extends editor_controller {
    
    protected $title = 'Пользователи';
    
    protected $_limit = 50;
    
    private $_ug;
    
    function action_after() {
        $this->_ug = $this->context->get_user_group_handle()->load();
        if (empty($this->params->op)) $this->_ug->is_render_by_key(true);
        $this->response->ug = $this->_ug->render();
    }
    
    function render_before() {                
         if (isset($_POST['filter'])) {
          $key = core::lib('db')->escape($_POST['filter']['title']);
          $this->set_where("LCASE(p1.nick) like '%{$key}%' OR LCASE(p1.login) like '%{$key}%'");   
          $this->renderer->set_data('_filter',  array('title' => $key));  
         }
        
    }
}


