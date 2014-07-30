<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: actions.php,v 1.1.2.2.2.3 2014/03/19 07:22:22 Vova Exp $
 */

return
    array(

        'card'        =>
            array(
                'url'   => '?m=infcp&c=card'
            , 'title'   => 'Карты'
            , 'default' => true
            ),

        'transaction' =>
            array(
                'url' => '?m=infcp&c=transaction'
            , 'title' => 'Транзакции'
            ),


        'actions'     =>
            array(
                'url' => '?m=infcp&c=actions'
            , 'title' => 'Действия'
            )


    );