<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2 2008/05/22 07:58:06 surg30n Exp $
 */  
      

 /*
CREATE TABLE "tf_test_images" (
  "id" int(10) unsigned NOT NULL auto_increment,
  "image" varchar(127) NOT NULL,
  PRIMARY KEY  ("id")
) */
      
/**
* test
* 
* type      - images
* storage   -  path under uploads
* thumbnail -  create thumbnail 100px/%
*/

class test_images_collection extends abs_collection {

       /*
       protected $fields = array(
          'id'               => array('type' => 'numeric')
        , 'title'            => array('type' => 'text')       
        , 'text'             => array('type' => 'text', 'no_format' => true)
        , 'image'            => array('type' => 'image', 'storage' => 'test', 'thumbnail' => 100) // 'max_width' => 100%*
       );
       */

}