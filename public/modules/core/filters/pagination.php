<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: pagination.php,v 1.1.2.1 2012/10/25 09:52:44 Vova Exp $
 */
 

class core_pagination_route_filter extends route_filter {
    
    protected $_regex = '@page\/(?P<page>\d+)$@';
    private   $_start = 0;
    
    function get_start() {
        return $this->_start;
    }
    
    function _match(&$uri, &$route) {
        $return = preg_match($this->_regex, $uri, $m);
        if ($return) {
            $this->_start = (int)$m['page'];
        }
        return $return;
    }
    
}