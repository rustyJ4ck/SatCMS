<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2.12.8 2013/12/23 08:10:31 Vova Exp $
 */     

class mail_tpl_collection extends abs_collection {
    
    /** @var  mail_tpl_item */
    protected $_last_mail;
    
    protected $fields = array(
    
        'id'                => array('type'=>'numeric'),
        'name'              => array('type'=>'text'         , 'size' => '255'),                  
        'title'             => array('type'=>'text'         , 'size' => '255'),
        // шаблон
        't_template'        => array('type'=>'text'         , 'no_format' => true),
        't_title'           => array('type'=>'text'         , 'size' => '255'),
        't_from_email'      => array('type'=>'text'         , 'size' => '127'),        
        
    );

    protected $formats = array(
        'editor' => array(
            'list' => array(
                't_template'        => array('hidden' => true),
                't_title'           => array('hidden' => true)
            )
        )
    );
    
    /**
    * @param mixed $name
    * @return mail_tpl_item
    */
    function get_mail_template($name) {
        if (!$item = $this->get_item_by_name($name)) {
            $item = $this->set_where("name = '{$name}'")->set_limit(1)->load()->get_item();
        }
        return $item;
    }
    
    function get_last_mail() {
        return $this->_last_mail;
    }
    
    /**
    * send
    */
    function send($name, $to, $params, $flip = false) {
        $this->_last_mail =
            $this->clear()
            ->set_where("name = '%s'", $name)
            ->set_limit(1)
            ->load()
            ->get_item();
        
        if (!$this->_last_mail) return false;
        
        return $this->_last_mail->send($to, $params, $flip);
    }
} 

/**
*   Элементы
*/  

class mail_tpl_item extends abs_collection_item {
    
    protected $_last_message;
    
    function get_last_message() {
        return $this->_last_message;
    }                                   
        
    function parse_params($text, $params) {
        if (!empty($params))
        foreach ($params as $k => $v) {                            
            if (is_string($v)) {
                $k = '%' . $k . '%';
                $text = strings::str_replace($k, $v, $text);
            }
        }
        $text = preg_replace('@\%.*\%@U', '', $text);
        return $text;
    }
          
    /**
    * постим
    */
    
    function send($to, $vars = array(), $flip = false) {
        
        $this->_last_message = '';
        
        if (empty($to)) {
            return false;
        }
        
        $return = true;
        
        $from       = $this->t_from_email;
   
        if ($flip) {
            $_from = $from;
            $from = $to;
            $to = $_from;
        }
        
        if (is_string($to)) 
            $to = (strpos($to, ','))  ? explode(',', $to) : array($to);
            
        $subject    = $this->t_title;
        
        $vars = array_merge($vars, array(
              'date' => date('d.m.Y H:i')
            , 'host' => (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost')
            , 'from' => $from
        ));
        
        $msg = $this->t_template;
        
        $msg = $this->parse_params($msg, $vars);
        $subject = $this->parse_params($subject, $vars);
        
        $this->_last_message = $msg;
        
        $i = 0;
        
        /** @var tf_mailer */
        $mailer = core::lib('mailer');
        
        foreach ($to as $to_email) {
            $to_email = trim($to_email);
            if (!empty($to_email)) {           
                  $params =   
                  array(
                    'from'         => trim($from),
                    'to'           => trim($to_email),
                    'msg'          => $msg,
                    'subject'      => $subject,
                    'is_html'      => 1
                  );
                  
                  if (!$i) {
                      if (isset($vars['bcc'])) $params['bcc'] = $vars['bcc'];
                      if (isset($vars['cc']))  $params['cc']  = $vars['cc'];
                  }  
                    
                  $return = $mailer->email($params); 
                  $i++;    
            }   
        }
        
        return $return;
        
    }
    
  }
  
