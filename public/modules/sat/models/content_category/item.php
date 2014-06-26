<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.4.2 2013/10/22 08:46:12 Vova Exp $
 */
 

class sat_content_category_item extends model_item {
    
    protected $_items;
  
    /**
    * Make url
    */
    function make_urls() {
     
        $url = core::module('sat')->get_router()->add_url_domain(
            '/'
            . $this->container->child_model
            .'/'
            . $this->slug . '/'
        );
            
        $this->append_urls('self', $url);
    }

    function get_type() {
        return $this->type_id ? core::module('sat')->get_content_types()->get_item_by_id($this->type_id) : false;
    }

    /**
    * Load news
    * 
    * @param mixed $num
    */
    /*
    function fetch_content($num = 2) {
        
        $this->_items =
            core::module('content')
                ->get_news_handle()   
                ->set_where("cat_id = %d", $this->get_id())
                ->set_limit($num)
                ->set_cfg_var('render_ios', true)
                ->set_working_fields($this->container->content_fields())
                ->load();    

            
         return $this->_items;
    }
    */

}