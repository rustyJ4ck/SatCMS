<?php
  
/**
 * Описание свойств подшаблонов сайта
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: layout.php,v 1.1.4.1.2.4 2012/05/18 12:11:46 Vova Exp $
 */  
 
class tf_layout extends abs_config {     
    
    private $_root;
    
    function __construct($root) {
        $this->init($root);
    }
    
    function init($root) {

        if ($root) {
        
            $this->_root = $root;
            $file = $this->_root . 'template.php';
            
            if (file_exists($file)) { 
                core::dprint(array('Load template config : %s', $file), core::E_DEBUG3);
                $this->init_config($this->_normalize(include($file)));
            }
        
        }
        
        return $this;
    }
    
    /**
    * Normilize config
    * @param mixed $c
    * @return mixed
    */
    private function _normalize($c) {
        if (!$c) return array();
        
        if (!empty($c['templates']))
        foreach ($c['templates'] as $kid => &$v) {
            if (!isset($v['name'])) $v['name'] = 'id_' . ($kid);
            if (!isset($v['title'])) $v['title'] =  preg_replace('@[-_]@', ' ', $v['name']);
            
            if (isset($v['extrafs'])) {
                if (!is_array($v['extrafs'])) {
                    if (!empty($v['extrafs']))
                        $v['extrafs'] = array($v['extrafs'] => true);
                    else 
                        $v['extrafs'] = array();
                }
                else {
                    if (isset($v['extrafs'][0])) {
                        // this is natural array, convert it
                        $vb = $v['extrafs']; $v['extrafs'] = array();
                        foreach ($vb as $vbv) $v['extrafs'][$vbv] = true;
                    }
                }
            }
            
            if (!isset($v['template'])) {
                $v['template'] = false; // default
            }
            
            if (isset($v['editor'])) {
                if (isset($v['editor']['tabs'])) {
                    foreach ($v['editor']['tabs'] as &$vet) {
                        if ($vet === false) {
                            $vet = array('disabled' => true);
                        }
                    }                    
                }                
            }
        }  
        return $c;                                  
    }
    
    function get_templates($with_default = false) {
        $templates = $this->cfg('templates', array());
        if (!$with_default && isset($templates[0])) {
            unset($templates[0]);
        }
        return $templates;
    }   
    
    function get_template_by_id($id) {
        return @$this->config['templates'][$id];
    }                       
    
}
