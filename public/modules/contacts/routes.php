<?php

/**
 * @package    inforkom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: routes.php,v 1.1.2.4 2012/10/25 09:52:42 Vova Exp $
 */  
  
return array(
       
    'qa' => array(
          'match'   => 'qa'
        , 'type'    => 'class'
        , 'title'   => 'Вопрос-ответ'
        , 'action'  => 'qa/list'
        , 'template'=> 'qa/list'
        , 'filters' =>  array('pagination') 
    ),    
    
    'qa/new' => array(
          'type'    => 'class'
        , 'title'   => 'Вопрос-ответ: Новый вопрос'
        , 'ajax'  => true
    ),      
    
    
    'qa/view' => array(
          'regex' => '@qa/view/(?P<id>[\w_\.\-\%а-я[:space:]]+)@u'
        , 'title' => 'Вопрос-ответ: Вопрос &laquo;%s&raquo;'
        , 'type'  => 'class'
    ),
    
    'qa/answer/new' => array(
          'type'    => 'class'
        , 'title'   => 'Вопрос-ответ: Новый ответ'
        , 'action'  => 'qa/answer_new'
        , 'template'=> false
        , 'ajax'  => true
    ), 

      
    'form/do' => array(
          'match'  => 'form/do*'
        , 'action' => 'form_do'
        , 'title' => 'Заявка'
    )
    
    ,    
    'mailer' => array(
        'template' => false        
        , 'type'  => 'class'
        , 'ajax'  => true
    )
    
);      

