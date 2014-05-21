<?php

/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: rss.php,v 1.1.2.1 2010/10/13 06:46:21 Vladimir Exp $
 */

/**
* RSS
*/
class sat_news_rss_action extends controller_action {
    
    private $_per_page = 10;
    
    function run() {

        $posts = core::module('sat')->get_news_handle()
                ->set_order('created_at DESC')
                ->set_limit(10)
                ->where('active', true)
                ->with_deps(array('category'))
                ->load()
                ->render();

        $parser = tpl_loader::get_parser(true);

        $parser->assign('site', core::module('sat')->get_current_site()->render());
        $parser->assign('posts', $posts);

        header('Content-Type: text/xml; charset=UTF-8');

        // display appends smarty_debug
        echo $parser->fetch('partials/sat/news/xml.tpl');

        core::selfie()->halt();

    }
    
}
