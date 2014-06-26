<?php

/**
 * @package    sestat
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.1 2011/11/17 07:26:51 Vova Exp $
 */

return array(


    'fields' =>
        array(
          'id'               => array('type' => 'numeric')
        , 'site_id'          => array('type' => 'numeric')

        , 'title'            => array('type' => 'text' , 'size' => 255)
        , 'slug'             => array('type' => 'text' , 'size' => 255, 'make_seo' => array('title', 1))
        , 'description'      => array('type' => 'text')

        , 'extra_fields'     => array('type' => 'array' , 'size' => 255)

    ),

    'formats' => array(
        'editor' => array(
            'form' => array(
                /*
                   'extra_fields'     => array(
                            'type' => 'virtual',
                            'method' => function($me){
                                 return @unserialize($me->extra_fields)?:array();
                             }
                    )
                */
            ),

            'list' => array(
                'site_id'     => array('hidden' => true),
                'description' => array('hidden' => true),

                'title'       => array('editable' => true),

                'extra_fields'     => array('hidden' => true),

                'url_children' => array('type'  => 'virtual',
                                        'class' => 'fit',
                                        'title' => 'Content',
                                        'method' => function($self){
                                                return '<a class="btn btn-xs btn-primary" href="'
                                                . $self->get_url('children') . '"><span class="glyphicon glyphicon-folder-open">'
                                                . '</span> &nbsp;Content</a>';
                                            })
            )
        )
    )

);