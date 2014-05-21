<?php
      
/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: mailer.php,v 1.1.2.3 2012/09/13 12:32:30 Vova Exp $
 */

/**
* Mail with template
*/
class contacts_mailer_action extends controller_action {
    
    function run() {  
    
        $name = functions::request_var('template', '');
        $name = $name ? $name : 'default';
        
        if (empty($name) || !preg_match('@^[a-z\d]+$@i', $name)) throw new controller_exception('Bad id');
        
        $domain = preg_replace('@^www\.@', '', $_SERVER['HTTP_HOST']);
        $vars = $this->request->post();
        $to   = $vars['email'];
        
        /** @var tf_mailer */
        $mailer = core::lib('mailer');
        $result = $mailer->email_template($name, $to, $vars, true);        

        
        $this->renderer
            ->set_ajax_result($result)
            ->set_ajax_message('ok')
            ->ajax_flush();
        
        return;
    }
    
}