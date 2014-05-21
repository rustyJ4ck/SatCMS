<?php

/**
 * @package    inforkom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: routes.php,v 1.1.2.3 2011/11/09 10:55:30 Vova Exp $
 */  
  
return array(

      'complete' => array(
        'template' => 'form/complete'
        , 'action' => false
      )

      , 'do' => array(
        'regex'       => '@^do/(?P<id>[\w\d_-]+)$@'
        , 'title'   => 'Анкета'  
        , 'action'  => 'form_do'
        , 'template'=> 'form/do'
        //, 'ajax' => true
    )
    
);      

