<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: logs.php,v 1.4.6.2 2011/12/22 11:28:45 Vova Exp $
 */

class core_feedback_controller extends editor_controller {

    protected $with_model = false;

    private function send_feedback() {

        $template = 'feedback';

        $vars = $this->request->post();
        $to   = $vars['email'];

        /** @var tf_mailer */
        $mailer = core::lib('mailer');

        $result = $mailer->email_template($template, $to, $vars, true);

        $this->set_message('Отправка сообщения', $result)
             ->set_message_data($vars);


    }

    function action_index() {

        if ($this->request->post('send')) {
            $this->send_feedback();
        }

    }

}