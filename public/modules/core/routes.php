<?php
return array(

    'api/editor/menu' => array(),
    'api/editor/i18n' => array(),

    'ctype/update' => array(
          'template' => false
        , 'action' => function(module_controller $ctrl){

                $request = $ctrl->get_request();

                /**
                 POST:
                 ctype	 : sat.node
                 field	 : title
                 id	     : 4
                 content : data
                */

                //@todo add some security checks

                $id = $request->post('id');
                $field = $request->post('field');
                $ctype = $request->post('ctype');
                $content = $request->post('content');

                $model = core::selfie()->get_ctype($ctype);

                if (!$model) {
                    throw new controller_exception('bad ctype');
                }

                $collection = $model->get_ctype_collection();

                if (!$collection) {
                    throw new controller_exception('No collection');
                }

                // allow only text fields
                if (!$collection->has_field($field) || (($fieldOptions = $collection->field($field)) && $fieldOptions['type'] != 'text')) {
                    throw new controller_exception('bad field');
                }

                $item = $collection->load_only_id($id);

                if (!$item) {
                    throw new controller_exception('No item');
                }

                $item->config->set('update_inline', true);

                $item->set_working_fields($field, 'updated_at');

                // deal with tinymce
                $content = html_entity_decode($content, ENT_NOQUOTES, 'UTF-8');

                $item->set_data($field, $content);
                $item->set_data('updated_at', time());

                $result = $item->save();

                // done
                $ctrl->get_renderer()
                    ->set_ajax_message('Изменения сохранены')
                    ->set_ajax_result($result)
                    ->set_ajax_data($request->post())
                    ->ajax_flush();
           }
    ),
/*
    'search/result' => array(
        'regex'     => '@^search\/(?P<id>\d+)(\/page\/(?P<page>\d+))?$@'
    , 'title'   => 'Поиск по сайту'
    , 'type'    => 'class'
    ),
*/
);