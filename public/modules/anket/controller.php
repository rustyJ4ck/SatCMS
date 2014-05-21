<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: controller.php,v 1.1.2.5.2.3 2012/09/18 13:10:42 Vova Exp $
 */
   
class anket_controller extends module_controller {  
    
    function action_form_do() {
        
        $aids = @$_COOKIE['anket'];
        
        $aids = explode(',', trim($aids));
        $aids = is_array($aids) ? $aids : array();
        
        $id = core::lib('db')->escape($this->get_param('id'));
        
        if (empty($id)) throw new controller_exception('Bad form id');
        
        // debug
        if (!core::is_debug() && in_array($id, $aids)) {
            $this->set_template('anket/form/already');
            return;
        }
        
        // load
        $form = 
        $this->get_context()->get_form_handle()
            ->set_where('name = "%s"', $id)
            ->set_limit(1)
            ->load()
            ->get_item();
            
        if (!$form) throw new router_exception('Form not found');
        
        $this->get_renderer()->set_current('anket_form'
            , $form->load_secondary()->render()
        );
        
        if ($this->request->post('form_submit') == 'yes') {
            
            $_post = $this->request->get_post();
            
            /** @var anket_result_collection */
            $pres  = $this->context->get_result_handle();                                     
            
            $pres->set_current_form($form);
            
            $_post['results'] = !empty($_post['q']) ? serialize($_post['q']) : '';
            $_post['uip']     = core::lib('auth')->get_user_ip(1);
            
            // debug
            // if (loader::in_ajax() !== true) die('juststopped');
            
            $pres->create($_post);
            
            if (!in_array($id, $aids)) $aids []= $id;
            setcookie('anket', join(',', $aids), time()+31104000, '/');                  
            
            $result = ($item = $pres->get_last_item()) ? $item->render() : false;
            
            if (!loader::in_ajax()) {
                $this->set_template('anket/form/complete');
                // result.form, result.option
                $this->renderer->set_return(
                   $result     
                );
                
            } else {
                $this->set_null_template();
                $this->renderer
                    ->set_ajax_message('Обработка запроса')
                    ->set_ajax_data($result)
                    ->set_ajax_result(true)
                    ->set_ajax_redirect('/anket/complete/');
            }
    
        }
    }
    
}