<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.3.2.3 2012/11/14 12:12:35 Vova Exp $
 */  
  
class sat_site_item extends abs_collection_item {
    
    protected $_current;
    protected $_force_static = false;
    
    /** @var array all domains occupied by site (+aliases) */
    protected $_domains;
    
    /**
    * Doamin matched in initial_route
    */
    function set_current() {

        $this->_current = true;
        
        if (0 != $this->template) {
            $templates = core::cfg('templates');
            
            if (isset($templates[$this->template])) {
                core::dprint(array('SITE_LAYOUT : %s', $templates[$this->template]), core::E_DEBUG1);
                core::lib('renderer')->set_layout($templates[$this->template]);
            }
        }
        
    }

    function get_domain() {
        if (core::is_debug()) {
            return empty($this->ddomain) ? $this->domain : $this->ddomain;
        }
        return $this->domain;
    }
    
    /**
    * Get all site domains
    */
    function get_domains() {
        if (!isset($this->_domains)) {
            $this->_domains = array(
                $this->domain
                , $this->ddomain
            );
            
            $aliases = explode(',', trim($this->aliases));
            
            if (!empty($aliases)) {
                $this->_domains = array_merge($this->_domains, $aliases);
            }
            
            foreach ($this->_domains as &$v) $v = trim($v);
        }         
        return $this->_domains;        
    }

    /**
     * self: http://domain.ru
     */
    function make_urls() {
        $this->append_urls('self', 'http://' . $this->get_domain() /*. '/' */);
    }
    
    function set_force_static($f) {
        $this->_force_static = $f;
    }
    
    function is_staticable() {
        return $this->b_static || $this->_force_static;
    }
    
    function clear_static() {
        
        if (!$this->get_domain()) return false;
        
        $root = core::module('sat')->get_static_root($this);
        $data = array();
        fs::build_tree($root, $data);
        
        foreach ($data['files'] as $f) {
            fs::unlink($f);
        }
        $data['dirs'] = array_reverse($data['dirs']);
        foreach ($data['dirs'] as $f) {
            fs::unlink($f, true);
        }        
    }
    
     /** clean */          
    function remove_before() {
        $this->clear_static();
        // remove nodes
        core::module('sat')->get_node_handle()
            ->set_where('site_id = %d AND pid = 0', $this->id)
            ->load()
            ->remove_all();
    }
    
    /**
    * Called on tf_sat::render (current.site.tree)
    */
    function with_tree() {
        if (!$this->isset_data('tree')) {
            $this->tree = $this->_get_tree(tf_sat::TREE_ID);
        }
        return $this;
    }
    
    function _get_tree($type = tf_sat::TREE_ID) {
        return  core::module('sat')->get_tree($this->id, $type);
    }
    
    /** 
    * Warn: load from db
    * @see self::_get_tree()
    * @see tf_sat::get_tree()
    */
        
    function get_tree() {         
        $ph = core::module('sat')->get_node_handle();
        $tree = $ph->get_tree($this->id);
        return $tree;
    }
}