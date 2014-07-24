<?php
/**
 * SatCMS  http://satcms.ru/
 * @author Golovkin Vladimir <rustyj4ck@gmail.com> http://www.skillz.ru
 */

return array(

    'fields'    => array(

              'id'         => array('type' => 'numeric')

            , 'pid'        => array('type' => 'numeric')

            , 'ctype_id'   => array('type' => 'numeric',
                                    'default' => 200,
                                    'title' => '@CT')

            , 'field'      => array('type' => 'text', 'size' => 127)
            , 'lang'       => array('type' => 'text', 'size' => 127)

            , 'value'      => array('type' => 'text', 'format' => false)


    )
);