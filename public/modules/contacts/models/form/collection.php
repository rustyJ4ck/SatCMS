<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.3 2012/09/18 16:07:40 j4ck Exp $
 */  
  
class contacts_form_collection extends model_collection {
    
        function set_notify_template($t) {
            $this->config->set('notify_template', $t);
        }
            
}


class contacts_form_collecton_LEGACY {

    function create_anket_result($id, $_post) {

        /** @var anket_form_item */
        $form =
            core::module('anket')->get_form_handle()
                ->set_where('id = "%d"', $id)
                ->set_limit(1)
                ->load()
                ->get_item();

        if (!$form) throw new collection_exception('Bad form id');

        /** @var anket_result_collection */
        $pres  = core::module('anket')->get_result_handle();

        $pres->set_current_form($form);

        $_post['results'] = !empty($_post['q']) ? serialize($_post['q']) : '';
        $_post['uip']     = core::lib('auth')->get_user_ip(1);

        // debug
        // if (loader::in_ajax() !== true) die('juststopped');

        $pres->set_notify_user(false)
            ->create($_post);

        return ($item = $pres->get_last_item()) ? $item : false;

    }

    function create_before(&$data, $id = null) {

        /** @var anket_result_item */
        $result = null;

        if (!isset($data['name'])) {
            $data['name']  = $data['u_name_2'] . ' ' .
                $data['u_name_1'] . ' ' .
                @$data['u_name_3'];
        }

        // with assigned anket
        if (isset($data['anket_id'])) {
            $result = $this->create_anket_result(
                (int)$data['anket_id'],
                $data
            );

            if ($result && ($result_option = $result->get_result_option())) {
                //set edu level from test result
                $data['f_level'] = $result_option->name;
            }
        }

        $data['result_id'] = $result ? $result->id : 0;

        $this->notify_user($data, $result);

    }

    /*
    Поступила заявка на обучение
    %date%, %from% %host%
    Имя %name%
    Телефон %phone%
    Email %email%
    Язык %f_lang%
    Тип %f_edu_type%
    Уровень %f_level%
    Комментарий %comment%
    Результаты теста:
    %test_result%
    */

    function notify_user($data, $result) {

        /*
        $mtpl = core::get_instance()->get_mail_tpl_handle()->get_mail_template('edu_request');
        */

        $data['test_result'] = ($result ? $result->text : '');

        /** @var tf_mailer */
        $mailer = core::lib('mailer');


        $mailer->email_template('edu_request'
            , ($data['email'] ? $data['email'] : 'test@domain.ru')
            , $data, true
        );
    }


}