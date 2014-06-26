<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.2 2010/07/21 17:57:21 surg30n Exp $
 */
 
class page_cache_item extends model_item {
    
    private $_expired = false;
    
    function __construct(model_collection_interface $container, $config = false, $data = false, $verified = false) {
        $return = parent::__construct($container, $config, $data, $verified);
        $this->_is_cached = !empty($this->expire_time);
        return $return;
    }
    
    /**
    * Hit and run
    * @param array additional data to store
    */
    function hit($optional = false) {
        
        $this->counter++; 
        $this->access_time = time();
        $this->sum_counter++;
        
        $upd_data = array(
              'counter'
            , 'access_time'   
            , 'sum_counter'  
        );
        
        // expire
        if ($this->expire_time != 0 && $this->expire_time < $this->access_time) {
            $this->_expired = true;
            $this->prev_access_time = $this->access_time;
            $this->expire_time = 0;
            $this->counter = 0;
            
            $upd_data[] = 'expire_time';
            $upd_data[] = 'prev_access_time';
        }
        
        // additional data
        if (!empty($optional)) {
            foreach ($optional as $k => $v) {
                $this->set_data($k, $v);
                if (!in_array($k, $upd_data)) $upd_data[] = $k;
            }              
        }
        
        $this->update_fields($upd_data);
    }
    
    /**
    * Cache hit
    */
    function cache_hit($time) {
        $this->hit(array(
            'expire_time' => (time() + $time)
        ));
    }
    
    /**
    * Is expired
    * Call after self::hit()
    */
    function is_expired() {
        return $this->_expired;
    }
    
    /**
    * Is cached entry
    */
    function is_cached() {
        return !empty($this->expire_time);
    }    
}