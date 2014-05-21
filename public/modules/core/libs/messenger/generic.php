<?php

/**
 * Core
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: generic.php,v 1.2 2010/07/21 17:57:21 surg30n Exp $
 */
  
class tf_messenger_generic {

    protected $_transport;
    protected $_to;
    protected $_from;
    protected $_subject;
    protected $_body;
    protected $_vars;
     
    protected $_connected = false;
                    
    function __construct($params = null) {
        $this->_create($params);          
    }
    
    function __destruct() {
        $this->close();
    }
    
    function clear() {
        $this->_transport = null;
        $this->_to = null;
        $this->_subject = null;
        $this->_body = null;
        $this->_vars = null;        
        return $this;
    }
    
    function connect($params = null) {  
        if (!$this->_connected) $this->_connected = $this->_connect($params);        
        return $this;
    }
    
    function close() {
        $this->_close();
        $this->_connected = false;   
    }

    
    /**
    * override constructor
    */
    function _create($params)  {;}
    /**
    * @return bool
    */
    function _connect($params) {;}   
    /**
    * close
    */
    function _close() {;}
    
    /**
    * @return tf_messanger_generic
    */
    function to($to) {
              
        if ($to === null || !is_array($this->_to)) $this->_to = array();
        
        if (!empty($to)) {
            if (!is_array($to)) $this->_to[] = $to;
            else 
            $this->_to = array_merge($this->_to, $to);
        }                 
        
        return $this;
    }

    /**
    * @return tf_messanger_generic
    */
    function subject($subject) {
        $this->_subject = $subject;
        return $this;
    }
    
    /**
    * @return tf_messanger_generic
    */
    function from($from) {
        $this->_from = $from;
        return $this;
    }    
    
    /**
    * @return tf_messanger_generic
    */
    function body($body) {
        $this->_body = $body;
        return $this;
    }    
    
    /**
    * @param array array()
    * @param mixed k => v
    * @return tf_messanger_generic
    */
    function assign($vars, $v = null) {
        
        if ($vars === null || !is_array($this->_vars)) $this->_vars = array();
        
        if ($v === null) {
            $this->_vars[$vars] = $v;
        }
        else {
            $this->_vars = array_merge($this->_vars, $vars);
        }                 
        return $this;
    }    
    
    function _parse() {
        $text = $this->_body;
        
        if (!is_array($this->_vars)) $this->_vars = array();
        $this->_vars['time'] = date('d.m.Y H:i');     
        
        $return = array();   

            foreach ($this->_vars as $k => $v) {
                $return['body']    = strings::str_replace("%{$k}%", $v, $this->_body);
                $return['subject'] = strings::str_replace("%{$k}%", $v, $this->_subject);
            } 
            
         return $return;                                                                
    }
    
    /**
    * @return mixed result
    */
    function send() {
                      
        if (empty($this->_to)) return false;
        
        $data = $this->_parse();
        
        if (!$this->_connected) {
            $this->connect();        
            if (!$this->_connected) return false;
        }
        
        $this->_send($data);         
    }
    
    /**
    * Real send
    * @param mixed $data
    */
    function _send($data) {;}
    
    
}