<?php

/**
 * Template config
 */

return array(

  'templates' => array(

     // defaults
     0 => array(),
  
     1 => array(
        'editor'  => array(    
            'tabs' => array(
                'images' => false,
                'files' => false
            )
        )
     ),

     2 => array(
         'title' => 'Как это работает',
         'name' => 'how-it-works',
         'template' => 'pages/howitworks',
         'extrafs' => array(1),
     ),

     3 => array(
         'title' => 'Опции',
         'name' => 'options',
         //'template' => 'pages/howitworks',
         'extrafs' => array(3),
     ),

     4 => array(
         'title' => 'Контакты',
         'name' => 'contacts',

         'site' => array(
             'item' => array('deps' => true)
         ),

       //  'controller' => 'contacts',
       //  'template' => 'pages/contacts',
         'extrafs' => array(3),
     ),

     19 => array(
           'title' => 'Вакансии',
           'child_template' => 20,     
     ),
     
     // Вакансии
     20 => array( 
           'title' => 'Параметры',   
           'name' => 'vacancy',
           'extrafs' => array(9),
     )  

));
