<?php

return
    array(

        'article'        => array(
            'url'     => '?m=test&c=article',
            'title'   => 'Статьи',
            'require' => array('js' => array('controllers/node')),
            'default' => true
        )

        , 'article_category' => array(
            'url'    => '?m=test&c=article_category',
            'title'  => 'Статьи - Категории',
            'hidden' => true
        )

    );