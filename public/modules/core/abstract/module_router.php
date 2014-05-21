<?php

/**
* @package TwoFace
* @version $Id: module_router.php,v 1.7.2.1.4.7 2012/12/11 18:00:17 j4ck Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/  
  
/**
* Module router
* @package core      
*/  
class module_router {
    
    const HTTP_PROTOCOL = 'http://';
    
    protected $_debug = false;
    
    /** @var core_module */
    protected $context;
    
    /** @var tf_request */
    protected $request;
    
    /** @var array routes */
    protected $_routes;
    
    /** current */
    protected $_route;
    
    /** uri after routing */
    protected $_uri;
    
    /** matched filters */
    protected $_filters = array();
      
    /**
    * Build new one
    * @param core_module to interact with
    */
    function __construct($context) {
        $this->context = $context;
        $this->request = core::lib('request');
    }
    
    /**
    * @return core_module
    */
    function get_context() {
        return $this->context;
    }
    
    /** @return tf_request */
    function get_request() {
        return $this->request;        
    }
    
    /**
    * remove static EXTension from string
    * @param &string url chunk
    */
    function trim_static_ext(&$val) {
        // @fixme move 'static_ext' into class
        $sx = core::get_instance()->get_cfg_var('static_ext');
        $sx_len = strlen($sx);
        if ($sx === substr($val, -1 * $sx_len, $sx_len)) {
            $val = substr($val, 0, -1 * $sx_len);
            return true;
        }
        
        return false;
    }
    
    /**
    * Load routes from file
    * or directly from module, if set
    */
    function load_routes() {
        
        // load from routes.php
        $routes = $this->context->get_routes();
        
        if (!$routes) {
            // load from routes.php
            $routes_file = $this->context->get_root() . 'routes.php';
            if (file_exists($routes_file))
                $routes = include $routes_file; 
        }
        
        return $routes;
    }
    
    /**
    * Route request
    * 
    * Warn! no exceptions
    * 
    * @return bool false if no routes found
    * @throws controller_exception, router_exception
    */
    function route($parts) {
        
        $this->_uri = implode('/', $parts);
        
		if (is_callable(array($this, 'route_before'))) 
            $this->route_before($parts);
        
        core::dprint(array('[route] %s using defaut router, mod: %s' 
            , $this->_uri
            , $this->context->get_name()));
        
        // give up loading routes if set in routers class
        if (empty($this->_routes)) $this->_routes = $this->load_routes();
        
        if (empty($this->_routes)) {
            core::dprint('Empty routes in ' . get_class($this), core::E_ERROR);
            return false;
        }
        
        foreach ($this->_routes as $id => $route) {
            
            // normalize
            if (!isset($route['match']) && !isset($route['regex']))     $route['match']     = $id; 
            if (!isset($route['action']))                               $route['action']    = $id;
            if (!isset($route['type']))                                 $route['type']      = 'method'; // class
            if (!isset($route['template']))                             $route['template']  = $id;

            if ($route['action'] instanceof Closure) {
                $route['type'] = 'inline';
            }

            if ($route['type'] == 'method') {
                $route['action'] = str_replace('/', '_', $route['action']);
            }
            
            // append section to match if any
            // if (isset($route['section']) && !empty($route['match']))    $route['match']     = $route['section'] . '/' . $route['match'];
            
            $this->_filters = array();
            $back_uri = $this->_uri;
            
            // match filters
            // all filters created before dispatch!
            $this->match_filters($route);  
                 
            // route
            $params = null;
            
            if ($this->_debug) {
                core::dprint('.. route ' . $id);
                core::dprint_r($route);
            }
            
            if ($this->_is_route_matched($route, $params)) {
                                                           
                // pure ajax routes
                if (isset($route['ajax']) && true !== loader::in_ajax()) {
                    throw new router_exception('Invalid query. Code.A6299');
                }

                if (isset($route['auth']['level'])) {
                    if ($route['auth']['level'] > core::lib('auth')->get_user()->level) {
                        throw new router_exception('Access denied. Code.A6298');
                    }
                }

                core::dprint(array('Route matched "%s"', $id));
                              
                $this->_route = $route;

                $this->context->get_controller()->run($route, $params);
  				$this->run_filters();
                return true;   
            }                  
            
            // restore uri, loop again?
            $this->_uri = $back_uri;
        }
        
        return false;
    }

    /**
    * Check route matched to uri
    * Extract params (only for regex <?Pname> routes)
    */
    private function _is_route_matched($route, &$params) {
        
        $match = isset($route['match']) ? $route['match'] : false;
        $uri   = $this->_uri;

        // wildcard: /url/blabla/*            
        if ($match) {
            if ($match == '*') { 
                $match = '';  $uri = '';
            }
            else
            
            if (strings::strpos($match, '*') !== false) {
                $match = strings::substr($match, 0, strings::strpos($match, '*'));
                $uri = strings::substr($uri, 0, strings::strlen($match));
            }
        }          
        
        return ((isset($route['regex']) && preg_match($route['regex'], $this->_uri, $params) && array_shift($params))
                || ($match !== false && $uri == $match));

    }
   
    function match_filters(&$route) {
        
        if (empty($route['filters'])) return;

        foreach ($route['filters'] as $filter) {
            /** @var route_filter */
            $pfilt = $this->create_filter($filter);
            if ($pfilt && $pfilt->match($this->_uri, $route)) { // uri altered, if matched                          
                $this->_filters[$filter] = $pfilt;   // matched, store
            }
        }
    }

    function run_filters() {
        if (empty($this->_filters)) return;
        foreach ($this->_filters as $filter)  $filter->run();
    }
    
    /**
    * @return route_filter
    */
    function get_filter($id) {
        return @$this->_filters[$id];
    }
    
    function get_filters() {
        return $this->_filters;
    }
    
    /**
    * @var string after|before
    * @var string module.path\filter_name
    * @return route_filter 
    */
    protected function create_filter($filter) {
        
        $pfilter= null;
            
        $fclass = $filter;
        
        // module.path_class  
        // @todo normalize path extractor

        if (($st = strpos($fclass, '.')) !== false) {
            $mod = substr($fclass, 0, $st);
            $filter = $fclass = substr($fclass, $st + 1);
            $pmod   = core::module($mod);
        }
        else        
            $pmod   = core::get_instance();
            
        $fclass = str_replace('/', '_', $fclass);
            
        $froot  = 'filters/';
        $fclass = $fclass . '_route_filter';
        
        // 1) try module
        
        $file = $pmod->get_root() . $froot . $filter . loader::DOT_PHP;
        $fclass_ns =  $pmod->get_name() . '_' . $fclass;
        
        if (!class_exists($fclass_ns, 0)) {
            if (file_exists($file))  require $file;
            else throw new router_exception(sprintf('Filter file not found %s', $file));
        }
            
        if (class_exists($fclass_ns, 0)) {
            $pfilter = new $fclass_ns;
        }
        
        if (!class_exists($fclass_ns, 0)) {
            throw new router_exception(sprintf('Filter class not found %s', $fclass_ns));
        }
        
        return $pfilter;
    }
    
    protected function append_filter($id, $pfilt) {
       $this->_filters[$id] = $pfilt;
       return $this;
    }
    
    /**
    * Get current uri
    * @return string /module/{this/uri/part/returned}/
    */
    function get_uri() {
        return $this->_uri;
    }

    /**
    * Get current
    */
    function get_current_route() {
        return $this->_route;
    }
    
    function set_action_title($title) {
        if (!$this->_route) $this->_route = array();
        $this->_route['title'] = $title;
        return $this;
    }    
    
    /**
    * Vsprintf curernt route title
    * @param vararg
    */
    function set_action_title_params() {
        $params = func_get_args();
        if (isset($this->_route['title'])) {
            $title = $this->_route['title'];
            $title = vsprintf($title, $params);
            $this->set_action_title($title);
        }
        return $this;
    }
    
    /**
    * Get current http proto
    * @todo proto configurable
    */
    function get_protocol() {
        return self::HTTP_PROTOCOL;
    }
    
    // @todo fix protocol
    // private $_protocol = 'http://';     
    
    /**
    * @return full main domain url
    */
    function make_url($url) {
        return $this->get_protocol() 
           . core::get_instance()->get_main_domain(true) 
           . (strpos($url, '/') !== 0 ? '/' : '')
           . $url;
    }
    
    /**
    * Make url with context    
    */     
    function normalize_url(&$url) {
        $core = core::get_instance();
        $base = $core->get_base_url();
        $url = $base . $url;
    }
    

    
}

/**
* Route Filter
*/

abstract class route_filter {

    /** If this matched, @see self::match() clear $uri part after self::_match return */
    protected $_regex;
    protected $_is_match;    
    protected $_result;
    
    function get_result() {
        return $this->_result;
    }
    
    function is_match() {
        return $this->is_match;
    }
    
    function match(&$uri, &$route) {
        $this->_is_match = $this->_match($uri, $route);
        
        // if matched and not empty regex, trim this part from uri
        if ($this->_is_match && $this->_regex) {
            $uri = preg_replace($this->_regex, '', $uri);
        }
        
        // clear trailing slash
        if (substr($uri, -1, 1) == '/') $uri = substr($uri, 0, -1);
        
        core::dprint(array('route_filter : %s, matched : %s (%s)', get_class($this), $this->_is_match?'YES':'NO', $uri));
        return $this->_is_match;
    }

    /**
     * Test filter without route
     * @param $uri
     * @return bool
     */
    function match_uri(&$uri) {
        $route = null;
        return $this->match($uri, $route);
    }
    
    /**
    * Check this filter match URI
    * @return bool matched?
    */
    abstract function _match(&$uri, &$route);
    
    /**
    * Run filter (after)
    */
    function run() {
    }
}