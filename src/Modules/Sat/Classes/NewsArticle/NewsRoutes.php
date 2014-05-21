<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.3 2014/01/23 07:56:30 Vova Exp $
 */

namespace SatCMS\Modules\Sat\Classes\NewsArticle;

class NewsRoutes {

    static function make($type) {

        return array(

            "{$type}/rss"  => array(
                  'type' => 'class'
                , 'title'  => 'Новости XML'
            ),

            "{$type}/list" => array(
                  'regex' => "@^{$type}(/(?P<category>[^/.]+))?$@"
                , 'type'    => 'class'
                , 'title'   => 'Новости'
                , 'filters' => array('pagination')
                , 'section' => $type
            ),

            "{$type}/item" => array(
                  'regex' => "@^{$type}(/(?P<category>[^/]+))?/(?P<id>[^\.]+)\.html$@"
                , 'type'    => 'class'
                , 'title'   => 'Новость'
                , 'section' => $type
                , 'filters' => array('sat.comment/modify')
            ),


        );
    }
}