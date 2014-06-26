<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.2 2010/07/21 17:57:20 surg30n Exp $
 */
 
class bans_item extends model_item {
    
    function __construct(model_collection_interface $container, $config = false, $data = false, $verified = false) {
        $return = parent::__construct($container, $config, $data, $verified);
        $this->active = (time() < ($this->time + $this->till));
        $this->ip_string = long2ip($this->ip);
        return $return;
    }
    
}