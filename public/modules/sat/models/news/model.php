<?php

return
    array('fields' =>
    array(
      'id'                => array('type' => 'numeric')

    , 'pid'               => array('type' => 'numeric')
    , 'site_id'           => array('type' => 'numeric')

    , 'title'             => array('type' => 'text',            'size' => 255)

    , 'slug'              => array('type' => 'text',
                                   'size' => 255,
                                   'make_seo' => array('key' => 'title', 'strict' => 1)
    )



    , 'author'            => array('type' => 'text',            'size' => 255)
    , 'keywords'          => array('type' => 'text',            'size' => 255)

    , 'description'       => array('type' => 'text',            'no_format' => true)
    , 'text'              => array('type' => 'text',            'no_format' => true)

    , 'h1_title'          => array('type' => 'text')
    , 'h1_description'    => array('type' => 'text',            'no_format' => true)


    , 'created_at'       => array('type' => 'unixtime', 'default' => 'now', 'autosave' => true)
    , 'updated_at'       => array('type' => 'unixtime', 'default' => 'now')

/*
    , 'files'             => array('type'   => 'virtual',
                                   'method' => 'get_attachs')
*/
    , 'image'     => array('type' => 'image'
        , 'spacing' => 1
        , 'storage' => 'news'
        , 'title' => 'Изображение'
        , 'remote' => true
            //, 'width' => 128, 'height' => 128
            //, 'thumbnail' => array(322, 222)

            , 'format' => array('resize', 800, 600, 'inside', 'down')
            , 'thumbnail' => array(
                    'format' => array('crop', 'center', 'center', 128, 96)
                )
        )

    , 'active'            => array('type' => 'boolean')
    , 'b_featured'        => array('type' => 'boolean', 'default' => 0)


    ),



    'formats' => array(
    'editor' => array(

        'form' => array(
            'image'       => array('description' => 'Изображение 800x600, мини 128x96')
        ),

        'list' => array(
            'pid'         => array(
                             'hidden' => true,
                             'filter' => array('params' => array('=', 'AND'))
             ),

            'site_id'     => array('hidden' => true),
            'description' => array('hidden' => true),
            'text'        => array('hidden' => true),
            'slug'        => array('hidden' => true),
            'author'      => array('hidden' => true),
            'keywords'    => array('hidden' => true),

            'h1_title'       => array('hidden' => true),
            'h1_description' => array('hidden' => true),

            'title'       => array('editable' => true /*, 'attrs' => 'style="width:80%"'*/,
                                   'filter' => array('params' => array('BEGINS', 'AND'))
                             ),

            'created_at'       => array(
                'filter' => array('params' => array('BETWEEN', 'AND'))
            ),

            'active'      => array('editable' => true, 'title' => 'A', 'description' => 'Active'),
            'b_featured'  => array('editable' => true, 'title' => 'F', 'description' => 'Featured'),

            /*
            'url_children' => array('type'  => 'virtual',
                                    'class' => 'fit',
                                    'title' => 'Children',
                                    'method' => function($self){ return '<a class="btn btn-xs btn-info" href="' . $self->get_url('children') . '"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;Children</a>'; })
            */
        )
    )
)

);


