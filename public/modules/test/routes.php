<?php
return array(

    '' => array(
        'action' => 'index',
        'layout' => 'simple',
        'type'   => 'class'
    ),

    'hello' => array(
        'action' => 'index',
        'layout' => 'simple',
        'type'   => 'class'
    ),

    'response/ok' => array(
        'action' => function(){
                return Response::make('hello', 404);
        }
    ),

    'response/ok2' => array(
      //  'method' => array('POST'),
        'action' => function(){

                /** @var tf_sat $sat */
                $sat = core::module('sat');

                return JsonResponse::make(['привет', 'пока'], 200);

            }
    ),

);