<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: module.php,v 1.1.2.10.2.9 2013/01/30 06:53:29 Vova Exp $
 */
  
class tf_sat extends core_module {

    /** @return sat_file_collection       */    function get_file_handle()          { return $this->model('file');         }
    /** @return sat_node_collection       */    function get_node_handle()          { return $this->model('node');         }
    /** @return sat_node_file_collection  */    function get_node_file_handle()     { return $this->model('node_file');    }
    /** @return sat_node_image_collection */    function get_node_image_handle()    { return $this->model('node_image');   }

    /** @return sat_site_collection */          function get_site_handle()          { return $this->model('site'); }

    /** @return sat_menu_collection       */    function get_menu_handle()          { return $this->model('menu');   }
    /** @return sat_comment_collection    */    function get_comment_handle()       { return $this->model('comment');   }

    /** @return search_collection */            function get_search_handle()        { return $this->model('search'); }
    /** @return search_result_collection */     function get_search_result_handle() { return $this->model('search_result'); }

    /** @return sat_widget_collection       */  function get_widget_handle()        { return $this->model('widget');   }
    /** @return sat_widget_group_collection */  function get_widget_group_handle()  { return $this->model('widget_group');   }

    /** @return sat_news_collection       */    function get_news_handle()          { return $this->model('news');   }
    /** @return sat_news_category_collection*/  function get_news_category_handle() { return $this->model('news_category');   }
    
    /** @return sat_site_item */ 
    private $_site;

    /**
     * Called in core::dispatch,
     * when sat is main module
     */
    function predispatch($domain, $path) {

        // fetch sat_site by domain/alias/path-prefix

        /** @var sat_site_item $site */
        $site = null;

        // try with prefix
        if ($this->cfg('route.site_path_prefix')) {

            $_path = explode('/', $path);

            if (count($_path) > 2) {
                $site  = $this->get_site_by_domain($domain, $_path[1]); // /{en}/

                if ($site) {
                    array_splice($_path, 0, 2);
                    // override request
                    $path = '/' . join('/', $_path);
                }
            }
        }

        // fetch without path
        if (!$site) {
            $site  = $this->get_site_by_domain($domain);
        }

        // default
        if (!$site) {

            if (!loader::in_ajax() && !core::in_editor()) {
                $this->core->shutdown_critical(<<<TPL
                    <code>
                    Domain {$domain} is not binded / configured.<br/>
                    Check your config or <a href="/editor/">login</a> to control panel.
TPL
                );
            }

            // fake site
            $site = $this->get_site_handle()->alloc(array(
                'id'        => 0,
                'domain'    => $this->core->get_main_domain(),
                'title'     => 'Unknown site',
                'active'    => true,
                'b_static'  => false
            ));
        }

        if (!$site) {
            /*
            if (!$skip_site_check) {
                throw new router_exception(
                    ((0 == $m_sat->get_site_handle()->count_sql())
                        ? 'No sites' : 'Invalid site'), router_exception::NOT_FOUND);
            }
            */
        } else {
            $this->set_current_site($site);
            core::dprint(array('Using site %d / %s', $site->id, $site->get_domain()), core::E_DEBUG1);

            // Update main domain
            $this->core->set_main_domain($site->get_domain());

            $site->set_current(true);

            // allow ajax-api when site disabled
            if (!$site->active && !loader::in_ajax()) {
                $this->core->error('Site inactive!', 200);
            }

            $site->set_force_static($this->cfg('sat_force_static', false));

            if ('/' == $path) {
                $this->core->in_index(true);
            }


        }
    }
    
    /**
    * @param mixed $id
    * @return sat_node_item
    */
    function get_node($id) {

        // get root
        if ($id === 0) {
            return $this->get_node_handle()->alloc(array(
                'title'      => 'Корень',
                'pid'        => 0,
                'id'         => 0,
                'site_id'    => $this->get_current_site_id(),
                'c_children' => 1
            ));
        }

        $node = $this->manager->get('node', $id);

        if (!isset($node)) {

            $node = $this->get_node_handle()
                ->load_only_id($id);

            $this->manager->set('node', $id, $node);
        }

        return $node;
    }

    function get_root_node() {
        return $this->get_node(0);
    }
    
    /**
    * @param mixed $id
    * @return sat_site_item
    */
    function get_site($id) {

        $sites = $this->manager->get('site', 'all');

        if ($sites && ($item = $sites->get_item_by_id($id))) {
            return $item;
        }

        $item = $this->manager->get('site', $id);

        if (!isset($item)) {
            $item = $this->get_site_handle()
                ->load_only_id($id);
            $this->manager->set('site', $id, $item);
        }
        return $item;
    }

    /**
     * @return sat_site_collection
     */
    function get_sites() {

        $sites = $this->manager->get('site', 'all');

        if (!isset($sites)) {
            $sites = $this->get_site_handle()->load();
            $this->manager->set('site', 'all', $sites);
        }

        return $sites;

    }
    
    /**
    * Update site tree cache
    * Eats resources!
    * 
    * @param mixed $site_id
    * @param mixed $with_sync
    */
    function update_tree($site_id = 0, $with_sync = false) {
        
        core::dprint(array('Update tree %d, sync %s', $site_id, $with_sync?'yes':'no'));
        
        $ph = $this->get_node_handle();
        
        // sync count
        if ($with_sync)
            $ph->sync_children_count($site_id);      
        
        // rebuild tree
        $tree = $ph->get_tree($site_id);
        
        // cache_it
        $this->_trees[$site_id] = $tree;

        // tree cacher file|apc|memcached
        // config: sat.tree_cacher = "apc"
        if (($scacher = $this->cfg('tree_cacher'))
            && ($ccacher = core::lib('cache')->get_engine($scacher))
        ) {
            $ccacher->set('site_tree_' . $site_id, $tree);
        }
        else {
        
            /** @var sape_cacher */ 
            $sc = core::lib('sape_cacher');                         
            $sc->set('tree', $site_id, $tree);  
        
        }
        
        return $tree;      
    }
    
    const TREE_ALL = 'all'; 
    const TREE_ID  = 'map';
    const TREE_URL = 'tree';
    
    private $_trees;
    private $_trees_cached = array();
    
    function is_tree_cached($site_id) {
        return in_array($site_id, $this->_trees_cached);
    }
    
    /**
    * @param mixed $site_id
    * @param mixed $type tree|map
    * 
    * tree[url] =>
    * map[id]   =>
    */
    function get_tree($site_id = 0, $type = self::TREE_URL) {
    
        if (!isset($this->_trees[$site_id])) {
            core::time_check('sat_get_tree', false, true);
            
            $from_cache = false;
            
            // config: sat_tree_cacher = "apc"        
            if (($scacher = $this->cfg('tree_cacher'))
                && ($ccacher = core::lib('cache')->get_engine($scacher))
            ) {
                $data = $ccacher->get('site_tree_' . $site_id);
                $from_cache = isset($data) ? true : false;
            }            
            else {
                /** @var sape_cacher */
                $sc = core::lib('sape_cacher');
                $data = $sc->get('tree', $site_id); 
                $from_cache = $sc->is_from_cache();        
            }      
            
            $this->_trees[$site_id] = ($from_cache) ? $data : array('map'=>array(), 'tree'=>array());
            if ($from_cache && !$this->is_tree_cached($site_id)) $this->_trees_cached  []= $site_id;
            
            core::time_check('sat_get_tree', false);
        }
        
        return $type == self::TREE_ALL ? $this->_trees[$site_id] : $this->_trees[$site_id][$type];
    }
    
    /**
    * Get tree item
    * @param int site_id
    * @param mixed url|id
    * @param int self::TREE_ID|URL
    * @return array tree node
    */
    function get_tree_item($site_id = 0, $id, $type = self::TREE_ID) {
                
        if ($type == self::TREE_URL && !empty($id)) {
            $tree = $this->get_tree($site_id, self::TREE_URL);
            $id = isset($tree[$id]) ? $tree[$id] : false;
        }
        
        if (empty($id)) return false;
        
        $tree = $this->get_tree($site_id, self::TREE_ID);
        return isset($tree[$id]) ? $tree[$id] : false;
    }
    
    /**
    * Get tree item by name, pid
    * 
    * @param mixed $name
    * @param mixed $pid
    * @param mixed $site_id
    */
    function get_tree_item_by_name($name, $pid = null, $site_id = null) { 
        $tree = $this->get_tree(isset($site_id) ? $site_id : $this->get_current_site_id(), self::TREE_ID);
                                
        foreach ($tree as $t) {
            if ($t['_url'] == $name && (!isset($pid)) || $pid == $t['pid']) return $t;
        }
        
        return false;
    }
    
    /**
    * @param string domain
    * @return sat_site_item
    */    
    function get_site_by_domain($domain, $path = '') {
        $path = $this->db->escape($path);
        return $this->get_site_handle()
            ->set_where("(domain = '$domain' OR aliases LIKE '%{$domain}%')")
            ->append_where("path = '{$path}'")
            ->set_order(false)
            ->load_first()
            ;
    }

    /** @return sat_node_collection */
    function get_root_nodes($site_id = null) {
        if (!$site_id) $site_id = $this->get_current_site_id();
        return $this->get_node_handle()
                ->set_where("site_id = %d AND pid = 0", $site_id)
                ->load();
    }                     
    
    /**
    * From initial route
    * 
    * @param sat_site_item $site
    */
    function set_current_site(sat_site_item $site) {
        $this->_site =  $site;
        
        // check for tree cache
        $this->get_tree($site->id);

        if (!$this->is_tree_cached($site->id)) {
            core::dprint('[Warn!] Regenerate tree cache');
            $this->update_tree($site->id);
        }
    } 
    
    function get_current_site() {
        return $this->_site ? $this->_site : false;
    }    
    
    function get_current_site_id() {
        return $this->_site ? $this->_site->id : false;
    }
    
    function get_current_site_tree($type = self::TREE_ID) {
        return $this->_site ? $this->get_tree($this->_site->id, $type) : false;
    }
    
    /**
    *  nav-chain
    * @return sat_node_collection 
    */
    function get_node_parents($id) {
        return $this->get_node_handle()->get_parents($id);
    }
    
    /**
    * @return string site static root
    */
    function get_static_root($site) {
        $domain = $site->get_domain(); 
        return loader::get_public() . 'static/'
            . $domain . '/';
    }

    /**
    * Called in non-debug mode!
    * Check for errors
    *     
    * @param mixed $node
    * @return string
    */
    function get_static_node_path($node) {   
    
        // @todo on error, $node empty too!
        
        $domain = '';
        
        if (!$node) {
            // this is / index
            $url = '/';
            $domain = $this->get_current_site()->get_domain();
        }
        else {       
            // node
            $url = $node->get_url();
            $domain = $node->get_site()->get_domain();
            
        }

        core::dprint(array('[!] Generate static %s', $url));
        
        $urls = explode('/', $url);

        foreach ($urls as &$u) {
            if (loader::is_windows()) {
                $u = iconv('UTF-8', 'WINDOWS-1251', $u); // @fixme windows?
            }
        }
        $url = join('/', $urls);
        
        if (substr($url, -1, 1) == '/') $url .= 'index.html';

        $url = loader::get_public() . 'static/'
            . $domain . '/'
            . substr($url, 1);    

        return $url;        
    }
    
    
    /**
    * Render
    * @param tf_renderer $r
    */
    
    function render(tf_renderer $r) {

        // Back
        if (core::in_editor()) {

            $site = null;
            $sites = $this->get_sites();

            $r->set_current('sites',
                $sites->render()
            );

            $site_id    = $this->request->get_ident('site_id');
            $site_id    = $site_id ? $site_id : $this->request->all('site_id');

            if (!$site_id) {
                $site = $sites->get_item();
            }
            else {
                $site = $sites->get_item_by_id($site_id);
            }

            if ($site) {
                $this->set_current_site($site);
            }

            $r->set_current('site', $site ? $site->with_tree()->render() : false);

        }
        else

        // Front
        if ($this->_site) {
            $r->set_current('site', 
                $this->_site->with_tree()->render()
            );               
        }
    }

    /**
        кэш только для основного домена.
        на алиасах не работает! 
    */
    function init91() {

        if (core::in_editor() || loader::in_ajax() || loader::in_shell()) return;
        
        // @todo cancel on errors!
        if (tf_exception::get_last_exception()) return;
        
        // check current site
        if (!($tsite = $this->get_current_site()) || !$tsite->is_staticable())  return;
        
        /* save static cache!
           skip logged in users, debug mode
        */
        if ($this->get_core()->cfg('sat_use_static')
            && !core::lib('auth')->logged_in() && !core::is_debug()
            ) {
            $file = $this->get_static_node_path(($tnode = $this->get_router()->get_current_node()));
           
            $pagination_filter = $this->get_router()->get_filter('pagination');     
            if ($pagination_filter && ($page = $pagination_filter->get_start())) {
                $file = str_replace('/index.html', "/page/{$page}/index.html", $file);
            }           
               
            core::dprint(array('generate staic : %s', $file), core::E_DEBUG4);
           
            if (!file_exists($file)) {
                $dir = dirname($file);
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                file_put_contents($file, core::lib('renderer')->get_buffer());
            }
        }
    }
    
    /**
     * @deprecated use renderer->get_templates
     */
    function get_templates() {
        return $this->core->get_cfg_var('templates');
    }
    
    /** @return sat_text_collection */ function get_text_handle() { return $this->model('text'); }
    
    /**
    * @return sat_text_item text
    * @param string name
    */                    
    function get_text($id, $site_id = false) {   
                                
        $cache_id = $id;  
        $data = $this->manager->get('sat_text_name', $cache_id);

        if (null !== $data) return $data;        
        
        $handle = $this->get_text_handle()
            ->set_where("name = '%s'", $id)
            ->append_where($site_id ? "site_id = {$site_id}" : false)
            ->set_order(false)
            ->load_first();
        
        $this->manager->set('sat_text_name', $cache_id, $handle);
        
        return $handle;
    }     
}
