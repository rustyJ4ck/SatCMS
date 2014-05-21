<?php

/**
 * Rss filter
 * 
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: embed.php,v 1.1.2.2 2012/09/18 09:23:26 Vova Exp $
 */
 
class embed_output_filter extends output_filter {   
    
    /**
    * Activate filter
    */
    function activate() {
        core::dprint('Activate RSS');
        // tpl_loader::set_template('');
        core::lib('renderer')->set_page_template('root.embed');
        tf_request::set_ident('embed', 'yes');
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
