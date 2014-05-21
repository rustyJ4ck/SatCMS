<?php

/**
 * Rss filter
 * 
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: rss.php,v 1.2 2010/07/21 17:57:21 surg30n Exp $
 */
 
class rss_output_filter extends output_filter {   
    
    /**
    * Activate filter
    */
    function activate() {
        core::dprint('Activate RSS');
        tpl_loader::set_template('rss');
        core::lib('renderer')->set_content_type('text/xml');
        // 'application/rss+xml'
    }  
    
    /**
    * Begin output event               
    */
    function on_output_begin() {
    }
    
    /**
    * Finish output
    * @param string buffered output
    * @return string output, in any
    */
    function on_output_finish($output) {
    } 
}
