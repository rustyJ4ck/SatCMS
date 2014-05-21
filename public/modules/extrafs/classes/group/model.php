<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.1.4.3 2012/05/17 08:58:19 Vova Exp $
 */  

return array(   
    'fields' => array(
          'id'              => array('type' => 'numeric')
          
        //, 'pid'             => array('type' => 'numeric')
        , 'site_id'         => array('type' => 'numeric')

        , 'title'           => array('type' => 'text', 'size' => 255)
        , 'name'            => array('type' => 'text', 'size' => 127, 'make_seo' => true)

        , 'description'     => array('type' => 'text', 'size' => 255)

	    , 'position'        => array('type' => 'position', 'space' => array('site_id'))
    ),


    'config' => array(
        //    'table'         => '%class%'
    ),

    'formats' => array(
        'editor' => array(
            'list' => array(
                'title'       => array('editable' => true),
                'description' => array('hidden' => true),
                'site_id'     => array('hidden' => true),
                'url_content' => array('type'  => 'virtual',
                                       'class' => 'fit',
                                       'title' => 'Fields',
                                       'method' => function($self){ return '<a class="btn btn-xs btn-info" href="' . $self->get_url('fields') . '"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;Fields</a>'; })
            )
        )
    )
);
