<?php          
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: config.php,v 1.3.2.2.2.3 2012/09/18 10:57:47 Vova Exp $
 */            

 /**
 * Config wrapper
 * @package core
 */
abstract class abs_config {
    
    const INIT_OVERRIDE = false;
    const INIT_APPEND = true;
    
    protected $config = array();
    
    /**
    * Init config 
    */
    function init_config($config = array(), $append = false) {
        
        if (empty($config)) return;
           
        if (!$append) {
            $this->config = $config;
        }
        else {
            $this->config = functions::array_merge_recursive_distinct($this->config, $config);
        }
    }

    /**
     * @return array
     */
    function get_config() {
        return $this->config;
    }
    
    /**
    * @see get_cfg_var 
    */
    function cfg($var, $default = null) {
        return $this->get_cfg_var($var, $default);
    }

    /**
    * get cfg var
    * @param mixed 'section.key'|'key'
    * @param mixed default value
    * @deprecated use @see cfg()
    * @return mixed
    */
    function get_cfg_var($key, $default = null) {
       $ret = null;

       // allow keys with 'dots'
       if (array_key_exists($key, $this->config)) {
           $ret = $this->config[$key];
       } else

       // section.name
       if (strpos($key, '.') !== false) {
           $ret = array_get($this->config, $key);
       }   
       else {
           $ret = @$this->config[$key];
       }

       // cast
       if (isset($default) && isset($ret)) {
          settype($ret, gettype($default));
       }
       
       if (!isset($ret)) $ret = !isset($default) ? null : $default;
       
       return $ret;             
    }

    /**
     * Unset
     * @param $var
     * @return $this
     */
    function unset_cfg_var($var) {

        // dirty
        if (strpos($var, '.') !== false) {
            array_set($this->config, $var, null);
        }
        else
        if (isset($this->config[$var])) {
            unset($this->config[$var]);
        }
        return $this;
    }                        

    /**
    * set config vars          
    * section.value = ''
    * value = ''
    * @return self
    */
    function set_cfg_var($var, $value) {
        
       // section.name
       if (strpos($var, '.') !== false) {
           array_set($this->config, $var, $value);
       }
       else {
           $this->config[$var] = $value;        
       }

       return $this;
    }
        
}