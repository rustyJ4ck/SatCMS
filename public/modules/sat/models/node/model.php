<?php
  
/**
 * @package    sestat
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.2.7.2.1 2012/05/17 08:58:20 Vova Exp $
 */  
  
return array(
              'id'               => array('type' => 'numeric')
            , 'pid'              => array('type' => 'numeric')
            , 'site_id'          => array('type' => 'numeric')
            
            , 'template'         => array('type' => 'numeric')  
            
            //  depricated, moved to layout config
            , 'child_template'   => array('type' => 'numeric')  
                    
            , 'position'         => array('type' => 'position', 'space' => array('site_id', 'pid'))
            
            , 'owner_uid'        => array('type' => 'numeric', 'autosave' => true)
            , 'modify_uid'       => array('type' => 'numeric')
              
            , 'title'            => array('type' => 'text' , 'size' => 255)
            , 'url'              => array('type' => 'text',  'size' => 255
                , 'make_seo' => array('key' => 'title', 'strict' => 1)
                , 'space' => array('site_id', 'pid'))
            
            , 'description'      => array('type' => 'text', 'no_format' => true)
            , 'text'             => array('type' => 'text', 'no_format' => true)

            , 'html_title'       => array('type' => 'text' , 'size' => 255)
            , 'html_text'        => array('type' => 'text', 'no_format' => true)
            
            , 'md'               => array('type' => 'text', 'size' => 255)
            , 'mk'               => array('type' => 'text', 'size' => 255)            
            
            , 'active'           => array('type' => 'boolean'    ,  'default' => true)               
            
            , 'updated_at'      => array('type' => 'unixtime', 'format' => 'd.m.Y H:i', 'default' => 'now')
            , 'created_at'      => array('type' => 'unixtime', 'format' => 'd.m.Y H:i', 'default' => 'now', 'autosave' => true)
            
            , 'c_children'       => array('type' => 'numeric', 'autosave' => true) 

            , 'active'           => array('type' => 'boolean',  /*'default' => true,*/ 'autosave' => true)

            , 'b_draft'          => array('type' => 'boolean',  'default' => false)               
            , 'b_system'         => array('type' => 'boolean',  'default' => false)               
            , 'b_approved'       => array('type' => 'boolean',  'default' => true, 'autosave' => true)       
            , 'b_featured'       => array('type' => 'boolean',  'default' => false)       
            
            , 'pagination'       => array('type' => 'numeric')  
            
            , 'image'           => array('type' => 'image'
                                //, 'spacing'     => 1     // set space to 2, if you has too much pics
                                , 'storage'     => 'node'
                                // , 'width'   => 148, 'height' => 98
                                , 'remote' => true
            )                  
);
