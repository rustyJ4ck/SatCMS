<?php

/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: view.php,v 1.1.4.6 2011/02/14 07:31:52 Vladimir Exp $
 */

/**
* News item
*/
class sat_news_item_action extends controller_action {

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
        $module     =  $this->_controller->get_context();
        
        $id         = $this->get_param('id');
        $category   = $this->get_param('category');

        /** @var categoryID $cat_filt */
        $cat_filt   = false;
         
        if ($category) {
            $hcat = $module->get_news_category_handle()
                ->where_slug($category)
                ->set_limit(1)
                ->load();
                
            if (!$hcat->count()) {
                throw new controller_exception('bad cat');
            }

            $cat_filt = $hcat->get_item()->id;
        }

        if (empty($id)) {
            throw new controller_exception('Bad id');
        }
        
        $uc = $module->get_news_handle();

        $u = $uc->with_deps(true /*['comments', 'images']*/)
            ->where('slug', $id)
            ->where('site_id', $module->get_current_site_id())
            ->append_where($cat_filt ? "pid = $cat_filt" : false)
            ->load_first();

        //    ->set_limit(1)
        //    ->load()
        //    ->get_item();
        
        if (!$u) {
            throw new controller_exception(i18n::T('sat\\No such news'));
        }

        // $u->load_secondary();

        /*
        $u->get_similar();  
        $u->get_attach_images();
        $u->get_comments()->load_secondary();
        $u->render_comments();
        */

        $this->renderer->return->news_item = $u->render();
        $this->renderer->set_page_title($u->title);
        
        $this->_controller->set_current_item($u);
        
        // see also 
        
        // [fav] @refactor dups
        /*
        if (!$this->_controller->get_user()->is_anonymous()) { 
            $hfavs = core::module('content')->get_fav_handle(); 
            $fav_group = $hfavs->get_fav_desc(1);
            $is_favorite = $hfavs->check_fav($u->id, $this->_controller->get_user()->get_id(), $fav_group['id']);
            
            $tpl_fav = array(
                'gid'       => $fav_group['id']
                , 'id'        => $u->id
                , 'active'    => $is_favorite
            );        
            
            $this->renderer->set_current('fav', $tpl_fav);     
        }
        */
        // [/fav]

    }
    
    
    
}
