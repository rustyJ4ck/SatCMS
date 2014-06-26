<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.3 2011/03/30 15:49:20 surg30n Exp $
 */

use SatCMS\Sat\Classes\Attachable\SlaveCollection as AttachableCollection;

/**
 * Class sat_node_image_collection
 */
class sat_node_image_collection extends AttachableCollection {

    protected $behaviors = array(
        'Sat.RemoteImage'
    );
    
    function load_for_site($id) {
        return $this->set_load_for_site($id)->load();
    } 
    
    function set_load_for_site($id) {
        $prefix = $this->db->get_prefix();
        $this->set_join("{$prefix}sat_node p2 ON p2.id = p1.pid")
            ->set_join_where('p2.site_id = %d', $id);
        return $this;
    }     
    
}
