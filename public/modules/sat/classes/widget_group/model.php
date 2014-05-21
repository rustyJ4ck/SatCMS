<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.1 2013/01/30 06:53:30 Vova Exp $
 */  

return array(
    'fields' =>
        array(
              'id'                => array('type' => 'numeric')
            , 'title'             => array('type' => 'text',        'size' => 255)
            , 'name'              => array('type' => 'text',        'make_seo' => 'title')
            , 'text'              => array('type' => 'text')
            , 'site_id'           => array('type' => 'numeric')
            , 'class'             => array('type' => 'text', 'size' => 255)
    ),

    'formats' => array(
    'editor' => array(
        'list' => array(

            'class'        => array('hidden' => true),
            'text'        => array('hidden' => true),
            'site_id'     => array('hidden' => true),

            'title'       => array('editable' => true),
            'name'        => array('editable' => true),

            'url_children' => array('type'  => 'virtual',
                                    'class' => 'fit',
                                    'title' => 'Widgets',
                                    'method' => function($self){ return '<a class="btn btn-xs btn-info" href="' . $self->get_url('children') . '"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;Widgets</a>'; })
            )
        )
    )

);

