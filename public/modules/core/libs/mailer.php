<?php

/**
* [lib_mailer]
* mailer = sendmail
*
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/

require 'ext/mailer/phpmailer.php';

/**
 * PHPMailer decorator
 * Class tfPhpMailer
 */
class tfPhpMailer extends PHPMailer {

    /**
     *  Method to send mail: ("mail", "sendmail", or "smtp").
     */

    function configure($c) {
        if (isset($c['mailer'])) {
            $this->Mailer = $c['mailer'];
        }
    }
}

/**
 * Mailer lib
 * Class tf_mailer
 */
class tf_mailer {
    
    private $config;
    
    private $_charset = 'UTF-8';
    
    /** @var mail_tpl_collection */
    private $_mail_collection;
    
    function configure($c) {
        $this->config = $c;
    }
    
    function set_charset($charset) {
        $this->_charset = $charset;
    }                            
 
  /**
   * @param array 
   *    array(
   *    'from', 
   *    'subject', 
   *    'to', 
   *    'msg', 
   *    'is_html'
   *   )
   * 
   * @return bool result
   */
   
     function email($m) {
        
       /* init with exceptions */
       $mailer                = new tfPHPMailer(true);
       
       $mailer->configure($this->config);
       
       $mailer->SMTPAuth      = false;
       $mailer->From          = $m['from'];    // отправитель
       $mailer->Subject       = $m['subject'];
       //$emailer->AltBody = 'Чтобы увидеть текст, включите отображение html в настройках почтового клиента.';
       $mailer->CharSet       = $this->_charset;
       $mailer->FromName      = empty($m['from_name']) ? '' : $m['from_name'];
              
       $mailer->ClearAddresses();
       $mailer->AddAddress($m['to']);

       $mailer->Body = $m['msg'];
       $mailer->isHTML(isset($m['is_html']));
                    
       if (!empty($m['bcc'])) {
           if (is_string($m['bcc']) /*&& strpos($m['bcc'], ',')*/) {
                $m['bcc'] = explode(',', $m['bcc']);
           }
           foreach ($m['bcc'] as $bcc) { $bcc = trim($bcc); if (!empty($bcc))  $mailer->AddBCC($bcc);   }
       }
       
       if (!empty($m['cc'])) {
            if (is_string($m['cc']) /*&& strpos($m['cc'], ',')*/) {
                $m['cc'] = explode(',', $m['cc']);
           }
           foreach ($m['cc'] as $cc) { $cc = trim($cc); if (!empty($cc))  $mailer->AddCC($cc);   }
       }
       
       $ret_message = '';
                   
       try {                            
           $ret = $mailer->send();
       }
       catch (phpmailerException $e){
           $ret = false;
           $ret_message = $e->getMessage();               
       }
       
       $ret_message = ($ret ? 'OK ' : 'FAIL ') . $ret_message;
                                                            
       // trace
       core::dprint(
            "emailer {$m['from']} --> {$m['to']} " . $ret_message
            , core::E_TRACE
       );       

       return $ret;
    }
    
    /**
     * @return mail_tpl_collection
     */
    function get_mail_collection() {
        if (!isset($this->_mail_collection)) {
            $this->_mail_collection = core::selfie()->model('mail_tpl');
        }
        return $this->_mail_collection;
    }
    
    /**
    * Email template
    * @param string template
    * @param array template vars
    * @param bool flip to/from
    * 
    * params:
    *  %vars% to template
    *  cc
    *  bcc
    */
    function email_template($name, $to, $params = array(), $flip = false) {
        
        if (empty($params) && !is_array($params)) $params = array();
        
        return $this->get_mail_collection()
            ->send($name, $to, $params, $flip);
        
    }

    
}
