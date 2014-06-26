<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.1.2.1.2.3 2012/09/18 13:10:42 Vova Exp $
 */
 
class anket_result_item extends model_item {
    
    /** @var anket_form_item */
    protected $_form;
    /** @var anket_result_option_item */
    protected $_result_option;
    
    /**
    * Создать отчет по результатам тестирования
    * html-output
    * 
    * @param mixed $q
    */
    function create_report($data) {

        /** @var smarty */
        $tpl_parser = core::lib('renderer')->get_parser();

        // [q] = [a]
        $options = @$data['q'];
        $options = is_array($options) ? $options : array();
        
        foreach ($this->_form->get_questions() as /** @var anket_question_item */ $aq) {
            $options_id = @$options[$aq->id];
            $aq->user_answer = $options_id ? $aq->get_answers()->get_item_by_id($options_id)->render() : array();            
        }
        
        $data['date'] = date('d.m.Y H:i');
        
        $this->_form->user_data = $data;
        
        $tpl_parser->assign('form', $this->_form->render());
        
        if ($ro = $this->get_result_option()) {
            $tpl_parser->assign('result_option', $ro->render());            
        }                   
        
        $return = $tpl_parser->fetch('anket/form/_result.tpl');
        
        if ($this->_form->notify_email) {
            $this->notify_user(trim($this->_form->notify_email), $return);
        }
        
        return $return;
        
    }
    
    function notify_user($email, $text) {
        
        if (!$this->get_container()->get_notify_user()) return;

        core::lib('mailer')->email(array(
              'from'    => 'info@' . ($domain = core::module('sat')->get_current_site()->get_domain())
            , 'to'      => $email
            , 'subject' => 'Пользователь заполнил анкету на сайте ' . $domain
            , 'msg'     => $text
            , 'is_html' => true 
        ));
    }
    
    function create_before($data) {
        $form = $this->get_container()->get_current_form();
        $data['pid']  = $form->id;
        
        $this->_form = $this->get_container()->get_current_form();
        $this->_form->load_secondary();
        
        $result = $this->calc_results(@$data['q']);       
        $data['value']    = $result['value'];
        $data['b_valid']  = $result['b_valid'];
        
        $data['text'] = $this->create_report($data);
    }
    
    function calc_results($options) {        
    
        $options = is_array($options) ? $options : array();
        
        $scores = 0;
        
        foreach ($this->_form->get_questions() as /** @var anket_question_item */ $aq) {
            $options_id = @$options[$aq->id];
            /** @var anket_answer_item */
            $answer = $options_id ? $aq->get_answers()->get_item_by_id($options_id) : false;         
            if ($answer) {
                $scores += ($answer->b_valid ? $answer->value : 0);
            }
        }
        
        $ro = $this->get_result_option($scores);
        
        $b_valid = $ro ? $ro->b_valid : false;
        
        return array(
            'value' => $scores,
            'b_valid' => $b_valid
        );      
        
    }
    
    function get_form() {
        if (!isset($this->_form)) {
            $this->_form = core::module('anket')->get_managed_item('form', $this->pid, array('with_module_prefix'=>1));
        }
        return $this->_form;
    }
    
    function load_secondary() {
        $this->get_form();
    }
    
    function render_after($data) {
        $data['form']   = isset($this->_form) ? $this->_form->render() : '';
        $data['option'] = isset($this->_result_option) ? $this->_result_option->render() : '';
    }
    
    function prepare2edt_before($data) {
        $data = $this->get_data();
    }
    
     /** @return anket_result_option_item */
    function get_result_option($value = null) {
        if (!isset($this->_result_option)) {
            $this->_result_option = core::module('anket')->get_result_option_handle()
                ->set_where('%d BETWEEN score_low AND score_high', (isset($value) ? $value : $this->value))
                ->set_limit(1)
                ->load()
                ->get_item();
        }
        return $this->_result_option;        
    }
    
}