<?php
    
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

/**
 * Class sat_menu_collection
 */
class sat_menu_collection extends model_collection {

    /**
    * Approve switch
    */
    function toggle_active($id, $value) {  
        $this->update_item_fields($id, 
            array('active' => $value)
        );                      
    }    
    
    /*
    function load_level(&$i) { 
        foreach ($this as $l) {
            $l->load_level($i);
        }
    }
    */
}

/**
 * Class sat_menu_item
 */
class sat_menu_item extends model_item {
    
    protected $_childrens;
    
    function load_secondary($options = null) {
        $this->get_childrens();
        return $this;
    }
    
    function get_childrens() {
        if (!isset($this->_childrens)) {
            $this->_childrens = core::module('sat')->get_menu_handle()
                ->set_where('pid = %d', $this->id)
                ->load();
        }
        return $this->_childrens;
    }
    
    function remove_after() {
        $this->get_childrens()->remove_all();
    }
    
    function load_level(&$i) {
        core::dprint(array('load:: %d - %s', $i, $this->title));
        $this->load_secondary();
        
        if ($i > 1) {
            --$i;
            foreach ($this->_childrens as $c) {
                $c->load_level($i);
            }
            
        }
        return $this;
    }
    
    function render_after($d) {
        $d['submenu'] = isset($this->_childrens) ? $this->_childrens->render() : false;
    }

    function make_urls() {

        if (core::in_editor()) {
            $this->append_urls('children', sprintf('?m=sat&c=menu&pid=%d', $this->id));
            $this->append_urls('self', sprintf('?m=sat&c=menu&pid=%d', $this->pid));
        }

    }
}