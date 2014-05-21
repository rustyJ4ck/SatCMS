<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.1 2013/01/30 06:53:30 Vova Exp $
 */
 
class sat_widget_group_item extends abs_collection_item {

    /**
     * @var sat_widget_collection
     */
    protected $_widgets;

    function get_widgets() {

        if (!isset($this->_widgets)) {
            $this->_widgets = core::module('sat')
                ->get_widget_handle()
                ->set_where('pid = %d', $this->id)
                ->load();
        }

        return $this->_widgets;
    }
    
    function remove_after() {  
        $this->get_widgets()->remove_all();
    }
    
    function load_secondary($options = null) {
        $this->get_widgets();
        return $this;
    } 
    
    function render_after($data) {
        if (isset($this->_widgets)) $data['widgets'] = $this->_widgets->render();
    }

    function make_urls() {

        if (core::in_editor()) {
            $this->append_urls('children', sprintf('?m=sat&c=widget&pid=%d', $this->id));
        }

    }
}