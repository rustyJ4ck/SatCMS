<?php
/**
 * Resource manager
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: manager.php,v 1.3 2008/05/25 15:49:48 surg30n Exp $
 */    

 /**
 * Manage 2 level lists run time cache
 * @todo integrate with cacher
 */
 class tf_manager {
     
     private $_data;

/*     
     function __construct() {
         $this->core = core::get_instane();
     }
*/
     
     /**
     * Check exists
     */
     function _isset($domain, $key) {
         return isset($this->_data[$domain][$key]);
     }
     
     /**
     * Get an item
     * 
     * Warn! Check for === null, not boolean
     * 
     * @return mixed|null
     */
     function get($domain, $key) {
         return $this->_isset($domain, $key) ? $this->_data[$domain][$key] : null;
     }
     
     /**
     * Set item
     */
     function set($domain, $key, $data) {
         if ($this->_isset($domain, $key)) return false;
         $this->update($domain, $key, $data);
         return true;
     }     
     
     /**
     * Force update
     */
     function update($domain, $key, $data) {
         $this->_data[$domain][$key] = $data;
     }
     
     
 }