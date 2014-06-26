<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.3 2008/05/07 07:46:37 j4ck Exp $
 */
 
class logs_item extends model_item {
     
     
    function prepare2edt() {        
        $data =  parent::prepare2edt();
        $data['data'] = str_replace("\n", '<br/>', $this->data);
        return $data;
    }
    
}
