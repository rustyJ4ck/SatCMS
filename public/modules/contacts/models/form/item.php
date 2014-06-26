<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.3 2013/01/30 06:53:28 Vova Exp $
 */
 
class contacts_form_item extends model_item {
    
    /** @var anket_result_item */
    protected $_anket_result;

    function get_anket_result() {
        if (!isset($this->_anket_result) && core::modules()->is_registered('anket')) {
            $this->_anket_result = core::module('anket')->get_managed_item('anket_result', $this->result_id, array('with_module_prefix'=>1));
        }
        return $this->_anket_result;
    }
    
    function load_secondary($options = NULL) {
        if ($options) {
            $this->get_anket_result();
        }
    }
    
    function render_after($data) {
        $data['anket_result'] = isset($this->_anket_result)
            ? $this->_anket_result->render()
            : '';
    }

    function create_before($data) {
        $data['uip'] = core::lib('auth')->get_user_ip(1);
    }

    /**
     * Notify user about
     * @param $data
     */
    function create_after($data) {

        $notify_tpl = $this->container->config->get('notify_template');

        if ($notify_tpl) {
            /** @var tf_mailer */
            $mailer = core::lib('mailer');

            $mailer->email_template($notify_tpl
                , ($data['email'] ? $data['email'] : 'test@domain.ru')
                , $data
                , true
            );
        }
    }
    
}