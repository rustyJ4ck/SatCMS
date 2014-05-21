<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.1 2009/12/31 20:11:57 surg30n Exp $
 */  
  
class sat_file_item extends abs_collection_item {

    function modify_before($data) {

        //if (!empty($data['file']['size'])) {
            $data['thumbnail'] = $data['file'];
        //}

    }

}