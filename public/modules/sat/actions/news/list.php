<?php

/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: list.php,v 1.1.4.4 2013/11/12 06:50:20 Vova Exp $
 */

/**
* Список
*/
class sat_news_list_action extends controller_action {
    
    private $_per_page;

    function construct_after() {
        $this->_per_page = $this->context->config->get('news.per_page', 10);
    }

    /*
    function nav_block() {
        \tr::get_instance()->sidebar->right->append('news_category', 
             $this->_controller->get_context()->get_news_category_handle()
                ->load()
                ->with_render_charset(false)
                ->render()    
        )->set_position(1);
    }
    */
    
    function run() {

        /** @var tf_sat $module */
        $module   =  $this->context;

        $hcat     = null;

        $category = $this->get_param('category');

        $pager = $this->router->get_filter('pagination');
        $page = $pager ? $pager->get_start() : 0;

        // catID
        $cat_filt   = false;
        
        if ($category) {
            $hcat = $module->get_news_category_handle()
                ->where_slug($category)
                ->load_first();
                
            if (!$hcat) {
                throw new controller_exception('bad cat');
            }

            $cat_filt = $hcat->id;
        }

        $cn = $module->get_news_handle()->set_order('created_at DESC');

        /** @var collection_filter*/
        $data = $cn
            ->with_deps(false)
            ->where('site_id', $module->get_current_site_id())
            ->append_where($cat_filt ? "pid = $cat_filt" : false)
            ->get_filter('/news/' . ($cat_filt ? ($hcat->slug . '/') : ''))
            ->with_clear(false)
            ->set_per_page($this->_per_page)
            ->set_pagination($page)
            //->on_load(function($collection){dd($collection);})
            ->apply(true);

        if ($cat_filt) {
            $this->renderer->set_page_title($hcat->title);
        }        

    }
    
}