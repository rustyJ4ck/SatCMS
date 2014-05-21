<?php

/**
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: controller.php,v 1.1.2.4 2012/09/18 13:10:43 Vova Exp $
 */
   
  
   
class contacts_controller extends module_controller {  


    /**
    * Форма заявка (на обучение)
    */
    
    function action_form_do() {

        $form_do_once = $this->context->cfg('form_do_once', false);
        
        /* extract params from uri, trim form/do
           Array (2)
            type => "mini"
            lang => "ru" 
        */
        $uri = array_slice(explode('/', $this->get_context()->get_router()->get_uri()), 2);
        $cdata = array();
        
        if (!empty($uri))
        foreach ($uri as $k => $u) {
            if ($k > 0 && $k % 2 != 0) continue;
            else
            if (isset($uri[$k+1])) {
                $cdata[$u] = $uri[$k+1];
            }
        }
        
        $aid = @$_COOKIE['request_form'];
          
        if ($form_do_once && !core::is_debug() && $aid) {
            $this->set_template('contacts/form/already');
            return;
        }
        
        $this->renderer->set_current('data', $cdata);
            
        if ($this->request->post('send')) {
            
            $post = $this->request->get_post();

            /** @var contacts_form_collection $pres */
            $pres  = $this->context->get_form_handle();                                     
            
            $post['uip']     = core::lib('auth')->get_user_ip(1);
            
            if (loader::in_ajax() !== true && !core::is_debug()) {
                die('juststopped');
            }

            $post['title'] = @$post['subject'];

            $pres->set_notify_template('feedback');

            $aid = $pres->create($post);
            
            setcookie('request_form', $aid, time()+31104000, '/');
            
            $this->set_null_template();
            
            $this->renderer
                ->set_ajax_message('Ваше сообщение отправлено')
                ->set_ajax_result(true)
                //redirect
                //->set_ajax_redirect('/contacts/form/complete/')
                ->ajax_flush()
            ;
    
        }
    }
    
}