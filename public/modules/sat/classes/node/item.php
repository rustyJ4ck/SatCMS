<?php
  
/**
 * @package    sestat
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.7.2.12 2013/09/29 10:41:20 jack Exp $
 */  

/*
 * extrafs to behavior
 *
if (!class_exists('extrafsable_collection_item', false)) {               
    class extrafsable_collection_item extends abs_collection_item {};   
}

  
class sat_node_item extends extrafsable_collection_item {

*/

class sat_node_item extends abs_collection_item {
    
    //protected $_with_render_cache = false;

    /** @var sat_site_item */
    protected $_site;

    /** @var sat_node_item */
    protected $_parent;

    /** @var sat_node_collection */
    protected $_children;
    
    protected $_url;
    
    protected $_node_files;
    protected $_node_images;
    protected $_files;
    protected $_owner; 
    
    protected $_template;

    /**
     * extrafs.fields behavior
     * @return array|null [id1, id2, ...]
     */
    function get_extrafs_ids() {
        $template = $this->get_template();

        $ids = !empty($template['extrafs']) ? array_keys($template['extrafs']) : false;
        return $ids;
    }

    /**
     * Updated with working-set
     * @param $data
     * @param $fields
     */
    function save_after($result) {

        /** site editor */
        if (!$this->config->update_inline || !$result) return;

        $fields = $this->get_working_fields();

        // update tree, if title changed
        if (array_intersect($fields, array('title'))) {
            core::module('sat')->update_tree($this->site_id);
        }

    }

    function modify_before($data) {

        // override template with child_template for new nodes
        if ($this->is_new() && $data['pid'] && ($parent = core::module('sat')->get_node($data['pid']))) {
            $tp = $parent->get_template();
            $data['template'] = isset($tp['child_template']) ? $tp['child_template'] : 0;                        
        }   
        
        // override model
        //if (!$this->is_new()) {
        //    $this->override_vfs();
        //}
    }
    
    /**
    * Default: load all
    * 
    * @param mixed array('files'=>bool, 'owner' =>bool, ...)
    * @return abs_collection_item
    */
    function load_secondary($options = null) {

        (is_array($options) && empty($options['files'])) or $this->get_files();        
        (is_array($options) && empty($options['owner'])) or $this->get_owner();
        (is_array($options) && empty($options['node_files'])) or $this->get_node_files();
        (is_array($options) && empty($options['node_images'])) or $this->get_node_images();

        // load other stuff (extrafs)
        parent::load_secondary($options);
    }
    
    function load_owner() {
        $this->_owner = core::module('users')->get_user($this->owner_uid);
        return $this->_owner;
    }
    
    function get_owner() {
        if (!isset($this->_owner)) {
            $this->load_owner();
            $this->owner = $this->_owner ? $this->_owner->render() : false;
        }
        return $this->_owner;
    }      

    function load_parent() {
        if (!isset($this->_parent)) {
            $this->_parent = $this->container->get_managed_item($this->pid);            
        }
        return $this->_parent;
    }
    
    /**
    * Get pagination with template overrides
    */
    function get_pagination() {
        $tpl = $this->get_template();
        return (isset($tpl['site']['pagination']) && empty($this->pagination)) 
            ? $tpl['site']['pagination'] : $this->pagination;
    }
    
    function get_parent() {        
        if (!$this->pid) return false;  
              
        if (!isset($this->_parent)) {
            $this->load_parent();
        }
        return $this->_parent;
    }    
    
    function render_parent() {
        if (!isset($this->parent)) {
            $p = $this->load_parent();
            $this->parent = ($p) ? $p->render() : false;            
        }
        return $this->parent;
    } 
    
    function load_site() {
        $this->_site = core::module('sat')->get_site($this->site_id);
    }
    
    function get_site() {        
        if (!$this->site_id) return false;        
        if ($this->_site === null) $this->load_site();
        return $this->_site;
    }      
    
    function clear_static($with_parent = true) {
        $s = $this->get_site();        
        if (!$s || !$s->is_staticable()) return;
        
        $file = core::module('sat')->get_static_node_path($this);
        fs::unlink($file);
        
        core::dprint(array('node::clear_static : %d [%d]', $this->id, $this->pid), core::E_DEBUG3);
        
        // unlink pagination chunks, yes its hack
        if (strpos($file, '/index.html') !== false) {
            $file = str_replace('/index.html', "/*/*/index.html", $file);
            foreach (glob($file) as $f) fs::unlink($f); 
        }                                              
        
        if ($with_parent && ($p = $this->get_parent())) {
            $p->clear_static(false);            
        }
    }

    /** 
    * @todo not tested
    * Loads all children
    */
    function load_children() {
        if (!isset($this->_children)) {
            $this->_children = $this->get_children_handle();            
        }
        
        core::dprint(array('sat::load_children, order %s', $this->_children->get_order()));
        $this->_children->load();
        
        return $this->_children;
    }
    
    /** @return sat_node_collection */
    function get_children_handle() {
        if (!isset($this->_children)) {
            $this->_children = core::module('sat')
                ->get_node_handle()           
                ->set_where(
                    "pid = %d", $this->id
                );
        }
        return $this->_children;
    }
    
    function set_children_handle($h) {
        $this->_children = $h;
    }
    
    function apply_children_filter($page) {
            $per_page   = $this->get_pagination();    
              
            if (!$per_page) {                
                $this->get_children();
                return false;
            }
            
            $children = $this->get_children_handle();
            $ch_filter = $children->get_filter($this->get_url());
            $fdata = $ch_filter->set_per_page($per_page)
                //->with_collection_render(false)
                ->set_pagination($page)
                ->apply(false);  
            
            $this->children_filter = $fdata->pagination;    
            return $fdata;
    }
    
    /**
    * use self::get_children_handle()
    * to apply children filters
    */
    function get_children() {        
        if (!$this->c_children) return false;        
        $this->load_children();
        return $this->_children;
    }        

    function load_files() {
        $this->_files = core::module('sat')->get_file_handle()
            ->set_where("pid = %d", $this->id)
            ->where('ctype_id', $this->get_ctype_id())
            ->load();
        return $this->_files;
    }
    
    function get_files() {
        if (!isset($this->_files)) {
            $this->load_files();
            //$this->_files->_fake_items(50);
            $this->files = $this->_files ? $this->_files->render() : false;
        }
        return $this->_files;
    }     
    
    function load_node_images() {
        $this->_node_images = core::module('sat')->get_node_image_handle()
            ->set_where("pid = %d", $this->id)
            ->where('ctype_id', $this->get_ctype_id())
            ->load();
        return $this->_node_images;
    }
    
    function get_node_images() {
        if (!isset($this->_node_images)) {
            $this->load_node_images();
            $this->node_images = $this->_node_images ? $this->_node_images->render() : false;
        }
        return $this->_node_images;
    } 
    
    function load_node_files() {
        $this->_node_files = core::module('sat')->get_node_file_handle()
            ->set_where("pid = %d", $this->id)
            ->where('ctype_id', $this->get_ctype_id())
            ->load();
        return $this->_node_files;
    }
    
    function get_node_files() {
        if (!isset($this->_node_files)) {
            $this->load_node_files();
            $this->node_files = $this->_node_files ? $this->_node_files->render() : false;
        }
        return $this->_node_files;
    }         
    
    function get_url() {        
        if (!isset($this->_url)) { 
            $tdata = core::module('sat')->get_tree_item($this->site_id, $this->id);
            $this->_url = $tdata['url'];
        }               
        return $this->_url;        
    }
    
    /**
    * Used in import
    * @todo optimize thru tree cache!
    */
    function _get_url() {
        
        if (!isset($this->_url)) {
            if (!$this->get_parent()) $this->_url = ($this->url);
            else {
                $this->_url = array();
                $item = $this->_parent;
                while(1) {
                    array_unshift($this->_url, ($item->url));
                    $item = $item->get_parent();
                    if (!$item) break;
                }
                $this->_url = /*site_url*/ '/' . implode('/', $this->_url);
                $this->_url []= ($this->url);
                $this->_url .= !$this->c_children ? '.html' : '/';
            }
        }
        
        return $this->_url;        
    }
    
    function make_urls() {

        $this->append_urls('self', core::module('sat')->get_router()->add_url_domain($this->get_url()));

        /** @var tf_editor $ed */
        $ed = core::lib('editor');
        $this->append_urls('editor_view', $ed->make_ng_url('?m=sat&c=node&pid=' . $this->id . '&site_id=' . $this->site_id, 1));
        $this->append_urls('editor_edit', $ed->make_ng_url('?m=sat&c=node&op=edit&id=' . $this->id . '&site_id=' . $this->site_id, 1));

        $this->append_urls('full', core::module('sat')->get_router()->url_with_host($this->get_url()));

        parent::make_urls();
    }

    /**
     * @param $data
     */
    function render_before($data) {
        $data['children'] = $this->_children ?  $this->_children->render() : false;
        $data['parent']   = $this->_parent   ?  $this->render_parent() /*$this->_parent->render()*/ : false;  
        $data['_template'] = $this->get_template();
        parent::render_before($data);
    }
    
    function prepare2edt_before($data) {
        $data['_template'] = $this->get_template();
        parent::prepare2edt_before($data);
    }
    
    /**
    * Node layout template
    */
    function get_template() {
        if (!isset($this->_template)) {
            // get node template, default is 0-key
            $this->_template = core::lib('renderer')->get_layout()->get_template_by_id($this->template);
        }
        return $this->_template;
    }   
    
    /** clean up */
    function remove_before() {

        if ($t = $this->get_children())     $t->remove_all();
        if ($t = $this->get_files())        $t->remove_all();
        if ($t = $this->get_node_files())   $t->remove_all();
        if ($t = $this->get_node_images())  $t->remove_all();

        parent::remove_before();
        
    }
    

  
}

/** Dummy node item */
class sat_node_item_dummy extends sat_node_item {
}
