<?php

/**
 * sat blocks
 * 
 * @package    content
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: blocks.php,v 1.1.4.5.2.7 2013/12/17 09:40:45 Vova Exp $
 */
  
class sat_blocks extends module_blocks {
    
    /**
    * predefined blocks
    */
    protected $_blocks = array(
          'last_nodes'      => array('template'  => 'node_list',    'title' => 'Последние записи')
        , 'similar_nodes'   => array('template'  => 'node_list',    'title' => 'Обратите внимание')  
        , 'rand_nodes'      => array('template'  => 'node_list',    'title' => 'Смотрите также')     
        , 'text'            => array('template'  => false)
        
        , 'nodes'           => array('template'  => 'node/list/default',     'title' => 'Список страниц')     
        , 'node'            => array('template'  => 'node/item/default',     'title' => 'Страница')     
        
        , 'menu'            => array('template'  => 'menu/default',     'title' => 'Меню')     
        
        , 'comments'        => array('template'  => 'comments/default',     'title' => 'Комментарии')     
        
        , 'widgets'         => array('template'  => 'widgets/default',     'title' => 'Виджеты')

    );
     
     
    /**    
    * NodesList Block
    * @param array (site_id, name[@parent], pid, count)
    * @return string list
    */      
    function nodes($params = false) {
        
        /** @var tf_sat */
        $content = $this->get_context();             
        /** @var sat_node_collection */
        $cdata = $content->get_node_handle();            
        
        if (isset($params->order)) {
            $cdata->set_order($params->order);
        }
        
        $site_id = isset($params->site) ? $params->site : $content->get_current_site_id();
        
        if (isset($params->name)) {
        
            $titem = $content->get_tree_item_by_name(
                $params->name
                , isset($params->pid) ? $params->pid : null
                , $site_id);
                
            if (!$titem) core::dprint(array('nodes block: no tree item with name : %s', $params->name));
            else {
                $params->pid = $titem['id'];
            }
             
            unset($params->name);
            
        }
        
        $where = isset($params->pid)  ? sprintf('pid = %d', $params->pid) : '';
        
        if (!empty($params->where)) {
            $where = (empty($where) ? '' : "{$where} AND ") . $params->where;
        }
        
        if (!empty($where)) {
            $where .= ' AND active';
        }
        
        $deps  = isset($params->attachs) ? (bool)$params->attachs : false;  
                
        return
        $cdata->with_deps($deps)
            ->set_where('site_id = %d', $site_id)
            ->append_where($where)
            ->set_limit(isset($params->count) ? $params->count : false)
            ->load()
            ->render();                
    }    
    
    /**    
    * NodesList Block
    * @param array (id)
    * @return string single
    */      
    function node($params = false) {
        
        /** @var tf_sat */
        $content = $this->get_context();    
                 
        /** @var sat_node_collection */
        $cdata = $content->get_node_handle(); 
        
        $with_children = isset($params->with_children) ? (bool)$params->with_children : false; 
        
        $where = isset($params->id)  ? sprintf('id = %d', $params->pid) : '';
        $where = isset($params->name) ? sprintf('url = "%s"', $params->name) : $where;  
        
        $deps  = isset($params->attachs) ? (bool)$params->attachs : false;                 
        
        $with_images = isset($params->with_images) ? (bool)$params->with_images : false;                 
        $with_files = isset($params->with_files) ? (bool)$params->with_files : false;             
        
        $with_extrafs = isset($params->with_extrafs) ? (bool)$params->with_extrafs: false;        
        
        /** @var sat_node_item */
        $item = $cdata
            ->set_limit(1)
            ->set_where(isset($params->where) ? $params->where : $where)
            ->load()
            ->get_item();

        if (!$item) {
            throw new block_exception('Node not found');
        }
                                                 
        if ($item && $deps)          $item->load_secondary();
        if ($item && $with_extrafs)  $item->get_extrafs_fields();        
        if ($item && $with_images)   $item->get_node_images();                                    
        if ($item && $with_files)    $item->get_node_files();
        if ($item && $with_children) {
             
            /** @var sat_node_collection */
            $childs = $item->get_children();
            
            // load children attachs
            if (isset($params->children_attachs) && $params->children_attachs) {
                $childs->load_secondary();
            }
        }
            
        return $item ? $item->render() : '';                
    }          
    
    /**    
    * Last Post Block
    * @param array (site_id, pid, count)
    * @return post
    */      
    function last_nodes($params = false) {
        $content = $this->get_context();             
        $cdata = $content->get_posts_handle();            
        $cdata->with_deps(false);
        $cdata->set_limit(20);
        $cdata->load();            
        $data = $cdata->render();                
        return $data;    
    }           
              
    /**
    * Similar posts
    * @param array (site_id, pid, count)
    */
    function similar_nodes($params) {        
        // 'pid' => int 558
        $count = isset($params->count) ? $params->count : 5;
        $ctx = $this->get_context();
        $ids = $ctx->get_tag2post_handle()->get_similar_posts_ids($params->pid, $count);
        if (empty($ids)) return false;
        
        $posts = $ctx->get_posts_handle();
        $posts
            ->with_deps(false)           
            ->set_where("id IN(%s)", implode(',', $ids))
            ->load();
        
        return ($posts ? $posts->render() : false);
    }
       
    /**
    * Similar posts
    * @param array (site_id, pid, count)
    */
    function rand_nodes($params = false) {        
        
        $count      = isset($params->count) ? $params->count : 5;
        $pid        = isset($params->pid)   ? $params->pid : false;
        $site_id    = isset($params->site_id) ? $params->site_id : $this->get_context()->get_current_site_id();
        
        $ctx = $this->get_context();
        
        $posts = ($ctx->get_node_handle()
            ->set_where('site_id = %d', $site_id)
            ->append_where($pid ? "pid = {$pid}" : '')
            ->append_where('c_children = 0')
            ->set_random_order()
            ->set_limit($count)
            ->load());
            
        return $posts ? $posts->render() : '';
    }  
     
    /**    
    * Text
    * @return text
    */
    function text($params = false) {
        if (empty($params->id)) return false;
        $cdata = $this->get_context()->get_text($params->id, core::module('sat')->get_current_site()->id); 
        return $cdata ? $cdata->text : "%{$params->id}%";    
    }

    /**    
    * Menu Block
    * @param array (site_id, name, level)
    * @return string list
    */      
    function menu($params = false) {
        
        /** @var tf_sat */
        $content = $this->get_context();             
        /** @var sat_node_collection */
        $cdata = $content->get_menu_handle();            
        
        $site_id = isset($params->site) ? $params->site : $content->get_current_site_id();
      
        $name  = isset($params->name) ? $params->name : '';
        $level = isset($params->level) ? (int)$params->level : 1;
        
        if (empty($name)) {
            core::dprint('Empty name in block sat::menu');
            return;
        }
        
        $cdata_item =
        $cdata->set_where('site_id = %d AND name="%s"', $site_id, $name)
            ->set_limit(1)
            ->load()
            ->get_item();
            
            
        if (!$cdata_item) {
             core::dprint('No such menu block sat::menu');
            return;
        }         
        
        return $cdata_item
            ->load_level($level)
            ->render();                
    }     
    
    /**    
    * CommentsList Block
    * @param array (pid, count)
    * @return string list
    */      
    function comments($params = false) {
        
        $pid        = isset($params->pid)   ? $params->pid : false;   
        
        if (!$pid) {
            core::dprint('Empty pid in block sat::comments');   
            return false;
        }
        
        /** @var tf_sat */
        $content = $this->get_context();             
        /** @var sat_comment_collection */
        $cdata = $content->get_comment_handle();            
        
        $ctype = 200;
        
        return
        $cdata->load_for_pid($pid, $ctype)->render();                
    }    
    
    /**    
    * Widgets Block
    * @param array (name[@parent], id)
    * @return string list
    */      
    function widgets($params = false) {

        /** @var tf_sat $module */
        $module = $this->get_context();            
        
        $where = 'site_id = ' . $module->get_current_site_id() . ' AND ';
        
        if (isset($params->name)) $where .= sprintf('name = "%s"', $params->name);
        else
        if (isset($params->id)) $where .= sprintf('id = "%d"', $params->id);
                         
        $widget_group = $module->get_widget_group_handle()
            ->set_where($where)
            ->set_limit(1)
            ->load()
            ->get_item();      
            
        if (!$widget_group) {
            core::dprint(__METHOD__ . ' widget group not found');
            return false;
        }
        
        $widget_group->get_widgets()->parse_data();
        return $widget_group->render();
    }      
    
}       
