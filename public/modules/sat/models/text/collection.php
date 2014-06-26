<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.1 2011/03/23 06:49:58 Vladimir Exp $
 */
class sat_text_collection extends model_collection {

    protected $key = 'name';

    protected $fields = array(
        'id'    => array('type' => 'numeric')
        , 'name'    => array('type' => 'text', /*'make_seo' => 'title'*/ 'editable' => true)
        , 'text'    => array('type'   => 'text',
                             'format' => false)
        , 'title'   => array('type' => 'text', 'editable' => true)
        , 'site_id' => array('type' => 'numeric')
    );

    protected $formats = array(
        'editor' => array(
            'list' => array(
                'site_id' => array('hidden' => true),
                'text' => array('hidden' => true)
            )
        )
    );


}