<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: router.php,v 1.1.2.5.2.8 2013/04/18 12:00:54 Vova Exp $
 */

/**
 * Class sat_router
 * @property tf_sat $context
 */
class sat_router extends module_router  {   

    private $_current_node;
    
    function get_current_node() { return $this->_current_node; }
    
    /**
    * Try base route,
    * otherwise try find out node
    *
    * @todo move stuff to controller
    */
    function route($parts) {

        // Try default routes
        if (false !== ($response = parent::route($parts))) {
            return $response;
        }

        $uri = implode('/', $parts);

        $in_index = (empty($parts));

        if ($in_index) {
            $this->render_index();
            return true;
        }

        $comment_filter = $this->create_filter('sat.comment/modify');
        $this->append_filter('comment_modify', $comment_filter);

        if (!$comment_filter->match_uri($uri)) {
            $comment_filter = null;
        }

        $pagination_filter = $this->create_filter('pagination');
        
        // append filter for later use
        $this->append_filter('pagination', $pagination_filter);
        
        $pagination_filter->match_uri($uri);
        
        // string(54) "14_dvigatel_mehanicheskaya_chast/klapanniy_zazor_2zzge"   
        $node_url = '/' . $uri;
        
        if (strings::substr($node_url, -5) != loader::DOT_HTML) $node_url .= '/';  
        
        $this->_static_node_url = $node_url;
             
        $c_site_id =  $this->context->get_current_site_id();
        
        $_item = $this->context->get_tree_item(
              $c_site_id
            , $node_url
            , tf_sat::TREE_URL
        );
        
        if (!$_item) {
            throw new router_exception('Node not found', router_exception::NOT_FOUND);
        }
        
        /** @var sat_node_item $item */
        $item = $this->context->get_node($_item['id']);

        if (!$item) {
            throw new router_exception('Node item not found', router_exception::NOT_FOUND);
        }    
        
        core::dprint(array('Found node %d, %s ', $item->id, $item->title)); 
        
        $tlayout = $item->get_template();                                 
        
        if (isset($tlayout['site']['order'])) {
            $item->get_children_handle()->set_order($tlayout['site']['order']);           
        }                                                                              
        
        core::event('sat_route_before', $item);       
        
        /* pagination filter */
        $page = $pagination_filter->get_start(); 
        
        try {
            $item->apply_children_filter($page);
        }
        catch (collection_filter_exception $e) {
            throw new router_exception($e->getMessage(), router_exception::NOT_FOUND);
        }
        
        $item->get_parent();

        // load secondary, if not disabled
        if (!isset($tlayout["site"]["item"]["deps"]) || $tlayout["site"]["item"]["deps"]) {

            $item->with_deps(@$tlayout["site"]["item"]["deps"]);
            $item->load_secondary(@$tlayout["site"]["item"]["deps"]);

        }
        
        $this->_current_node = $item;

        // for comments bindings
        $this->context->controller->set_current_item($item);

        if ($comment_filter) $comment_filter->run();
        
        /** @var tf_renderer */
        $renderer = $this->context->renderer;

        //
        // template alternative layout {layout}.tpl
        //
        if ($tlayout['template']) {
            $renderer->set_page_template(
                /*'root.' .*/ $tlayout['template']
            );
        }
        
        core::event('sat_render_before', $item);  
                                          
        $renderer->current->node = $item->render();
        $renderer->current->node_chain = $this->context->get_node_parents($item->id)->render();

        $renderer->set_main_template(
            ($tpl = $this->context->get_controller()->get_template())
            ? $tpl
            : 'sat/node/item'
        );  
        
        return true;
        
    }

    /**
     * Frontpage
     */
    function render_index() {

        $this->context->renderer
            ->set_current('nodes', $this->context->get_root_nodes()->render())
            ->set_main_template('index');

    }

    /**
     * @param $url
     * @return string
     */
    function url_with_host($url) {
        return self::HTTP_PROTOCOL
            . (($site = $this->context->get_current_site()) ? $site->domain : 'no.site.domain')
            . $url;
    }
    
    /**
    * Adds domain part to url
    * used in multisite config, when accessing another domain resources
    */
    function add_url_domain($url) {
        /** @var sat_site_item */
        $psite = $this->context->get_current_site();
        
        if ($psite && !in_array($this->get_request()->get_host(), $psite->get_domains())) {
            $url = $this->url_with_host($url);
        }
        
        return $url;
    }

    
}
