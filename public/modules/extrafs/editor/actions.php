<?php

/**
 * contentz blockz
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: actions.php,v 1.1.4.2 2011/10/19 06:45:28 Vova Exp $
 */

return
    array( // default = true
        'group'   => array('url' => '?m=extrafs&c=group', 'title' => 'Группы', 'default' => true)
        , 'field' => array('url' => '?m=extrafs&c=field', 'title' => 'Доп. поля', 'hidden' => true,
              'require' => array(
                'js' => array('controllers/extrafs')
            ))
              //, 'value'              => array( 'url' => '?m=navigator&c=polyline_type',         'title' => 'Типы отрезков')
 );
