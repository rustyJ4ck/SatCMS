<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: routes.php,v 1.1.2.1.4.1 2012/10/18 06:59:59 Vova Exp $
 */

return array(

//
// Api
// ----------------------------------------------------------------------


    'api/editor/sites' => array(),

    'api/editor/node/tree' => array(
        'regex'     => '@^api/editor/node/tree/(?P<id>\d+)$@'
    ),


//
// News
// ----------------------------------------------------------------------

    'news/rss' => array(
          'type'      => 'class'
        , 'title'     => 'Новости XML'
    ),

    'news/list' => array(
        'regex'       => '@^news(/(?P<category>[^/.]+))?$@'
        , 'type'      => 'class'
        , 'title'     => 'Новости'
        , 'filters'   =>  array('pagination')
        , 'section'   => 'news'
    ),

    'news/item' => array(
        'regex'       => '@^news(/(?P<category>[^/]+))?/(?P<id>[^\.]+)\.html$@'
        , 'type'      => 'class'
        , 'title'     => 'Новость'
        , 'section'   => 'news'
        , 'filters'   =>  array('sat.comment/modify')
    ),


//
// Search
// ----------------------------------------------------------------------

    'search/tree' => array(
        'type'    => 'class'
    ),

    'search/suggest' => array(
        'title'     => 'Поиск по сайту'
        , 'type'    => 'class'
        , 'match'   => 'search/suggest*'
    ),

    'search/result' => array(
        'regex'     => '@^search\/(?P<id>\d+)(\/page\/(?P<page>\d+))?$@'
        , 'title'   => 'Поиск по сайту'
        , 'type'    => 'class'
    ),

    'search/query' => array(
        'regex'     => '@^search(\/q)?(\/(?P<query>[^\/]+))?$@'
        , 'title'   => 'Поиск по сайту'
        , 'type'    => 'class'
    ),
);