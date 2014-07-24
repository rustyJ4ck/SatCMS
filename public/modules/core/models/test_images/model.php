<?php

return
    array(
        'fields'  =>
            array(
                  'id'  => array('type' => 'numeric')
                , 'title' => array('type' => 'text')

                , 'slug'  => array(
                        'type' => 'text',
                        'size' => 255,
                        'make_seo' => array('key' => 'title', 'strict' => 1)
                  )

                , 'text'  => array('type' => 'text', 'no_format' => true, 'hidden' => false, 'lang' => true)
                , 'image' => array('type' => 'image', 'storage' => 'test', 'thumbnail' => 100) // 'max_width' => 100%*

                , 'created_at'       => array('type' => 'unixtime', 'default' => 'now', 'autosave' => true)
                , 'updated_at'       => array('type' => 'unixtime', 'default' => 'now')
            ),


        'behaviors' => array(
            'Core.Multilang'
        ),

        'formats' => array(

            'site'   => array(),

            'editor' => array(

                'default' => array(),

                'list'    => array(
                    'text' => array('hidden' => true),
                    'virtual_field' => array('type' => 'virtual', 'method' => function($self, $type){ return $type . '#' . $self->id; })
                ),

                'form'    => array()
            )

        )

    );