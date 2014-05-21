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
            , 'pid'              => array('type' => 'numeric')
            , 'site_id'              => array('type' => 'numeric')
            
            , 'position'         => array('type' => 'position', 'space' => 'pid')
                    
            , 'title'            => array('type' => 'text' , 'size' => 255)
            , 'name'             => array('type' => 'text' , 'size' => 255, 'make_seo' => array('title', 1))
            , 'description'      => array('type' => 'text')
            , 'url'        => array('type' => 'text',  'size' => 255)
            
            , 'active'           => array('type' => 'boolean'    ,  'default' => true)               

    ),

    'formats' => array(
        'editor' => array(
            'list' => array(
                'pid'         => array('hidden' => true),
                'site_id'     => array('hidden' => true),
                'description' => array('hidden' => true),

                'title'       => array('editable' => true),
                'name'        => array('editable' => true),
                'url'         => array('editable' => true),
                'active'      => array('editable' => true),

                'url_children' => array('type'  => 'virtual',
                                       'class' => 'fit',
                                       'title' => 'Children',
                                       'method' => function($self){
                                               return '<a class="btn btn-xs btn-info" href="'
                                                    . $self->get_url('children') . '"><span class="glyphicon glyphicon-folder-open">'
                                                    . '</span> &nbsp;Children</a>';
                                           })
            )
        )
    )

);
