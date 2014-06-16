<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: _req.php,v 1.1.2.6.2.2 2011/12/22 11:28:47 Vova Exp $
 */

namespace SatCMS\Sat\Editor\Controllers;

use core, editor_controller;

/**
 * Class SatController
 * @package SatCMS\Sat\Editor\Controllers
 */
class SatController extends editor_controller {

    /** @var int current siteID */
    protected $site_id;
    
    /** @var \sat_site_collection */
    protected $sites;

    /** @var \sat_site_item */
    protected $site;

    /**
     *
     */
    function construct_before() {
        $this->_init_sat_controller();
        parent::construct_before();
    }

    /**
     *
     */
    private function _init_sat_controller() {

        // @todo merge this method with tf_sat::render

        $this->renderer = core::lib('renderer');

        /** @var \tf_sat $pmsat */
        $pmsat            = core::module('sat');

        $this->site_id    = $this->params->get('site_id');
        $this->site_id    = $this->site_id ? $this->site_id : $this->request->all('site_id');
                                                                      
        //$this->sites      = $pmsat->get_site_handle()->load();

        $this->sites        = $pmsat->get_sites();

        /*
        $this->renderer->set_data('sat_sites',
           $this->sites->render()   
        );
        */
        
        // get default

        if (!$this->site_id) {
             $this->site = $this->sites->get_item();
        }
        else {
            $this->site = $this->sites->get_item_by_id($this->site_id);
        }
        
        // if no parent, you'll die sooon :)
        
        $this->site_id = 0;

        if ($this->site) {
            
            $this->site_id = $this->site->id;
            
            if (@$_COOKIE['site_id'] != $this->site_id)
                setcookie('site_id', $this->site_id, time() + 24 * 3600 * 30, '/editor/');
                
            $pmsat->set_current_site($this->site);
            
            // check site has cached tree
            $pmsat->get_tree($this->site_id);

            // @todo already done in sat::set_current_site
            if (!$pmsat->is_tree_cached($this->site_id)) {
                core::dprint(array('Generate tree : %d', $this->site_id));
                $pmsat->update_tree($this->site_id);
            }

            $this->renderer->set_current('site',
                $this->site->with_tree()->render()
            );
        }
        
        else {
            
            // выбранный сайт не существует 
            
        }
    }

    function render_sites() {
        $this->response->sites = $this->get_sites()->render();
    }

    /** current site id */
    function get_site_id() {
        return $this->site_id;
    }
    
    /** current site */
    function get_site() {
        return $this->site;
    }
    
    /** all sites */
    function get_sites() {
        return $this->sites;
    }
}        

