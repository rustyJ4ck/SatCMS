<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: model.php,v 1.1.4.3 2011/02/11 07:50:35 Vladimir Exp $
 */


namespace SatCMS\Sat\Classes\NewsArticle;

class CategoryModel {

    static function make() {

        return

            array(

                'fields' =>

                    array(
                        'id'     => array('type' => 'numeric')
                    , 'position' => array('type' => 'position', 'space' => 'site_id')

                    , 'site_id'  => array('type' => 'numeric', 'hidden' => true)

                    , 'title'    => array('type' => 'text', 'size' => 127, 'editable' => true)
                    , 'slug'     => array('type' => 'text', 'size' => 127, 'make_seo' => 'title')

                        // sync children count
                    , 'c_count'  => array('type' => 'numeric', 'autosave' => true, 'title' => 'Количество')

                    ),

                'config'
                         => array( // 'order_sql'     =>  //'substr(title,1,4)'
                    )

            );


    }


}