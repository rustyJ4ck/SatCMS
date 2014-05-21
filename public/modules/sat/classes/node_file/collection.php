<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.2 2011/03/23 09:11:37 Vladimir Exp $
 */  
  
class sat_node_file_collection extends abs_collection {
    
    function load_for_site($id) {
        $prefix = $this->db->get_prefix();
        return
        $this->set_join("{$prefix}sat_node p2 ON p2.id = p1.pid")
            ->set_join_where('p2.site_id = %d', $id)
            ->load();
    } 
    
}