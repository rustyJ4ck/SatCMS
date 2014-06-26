<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: actions.php,v 1.1.2.2.2.3 2014/03/19 07:22:22 Vova Exp $
 */
        
       return
        array(
            'site'           => array('url' => '?m=sat&c=site'          , 'title' => 'Сайты')

          , 'node'           => array('url' => '?m=sat&c=node'          , 'title' => 'Страницы'
                , 'require' => array('js' => array('controllers/node', 'directives/node'))
                , 'default' => true
          )

          , 'news'           => array('url' => '?m=sat&c=news',
                                          'title' => 'Новости',
                                          'require' => array('js' => array('controllers/node'))
          )

          , 'news_category'   => array('url' => '?m=sat&c=news_category',
                                        'title' => 'Новости - Категории',
                                        'hidden' => true
          )

          , 'comment'        => array('url' => '?m=sat&c=comment'          , 'title' => 'Комментарии')

          , 'text'           => array('url' => '?m=sat&c=text'          , 'title' => 'Снипеты')
          , 'menu'           => array('url' => '?m=sat&c=menu'          , 'title' => 'Меню')

          , 'widget_group'       => array('url' => '?m=sat&c=widget_group'  , 'title' => 'Виджеты')
          , 'widget'            => array('url' => '?m=sat&c=widget'  , 'title' => 'Виджет', 'hidden' => true)

          , 'fm'                 => array('url' => 'fm/'          , 'title' => 'Менеджер файлов')


          , 'content_type'      => array('url' => '?m=sat&c=content_type'          , 'title' => 'Content types')
          , 'content_category'  => array('url' => '?m=sat&c=content_category'      , 'title' => 'Content categories')

          , 'content'           => array('url' => '?m=sat&c=content'          , 'title' => 'Content',
                'require' => array('js' => array('controllers/node'))
            )

          , 'node_image'            => array('url' => '?m=sat&c=node_image'  , 'title' => 'Картинки', 'hidden' => true)
          , 'node_file'            => array('url' => '?m=sat&c=node_file'  , 'title' => 'Файлы', 'hidden' => true)

        );