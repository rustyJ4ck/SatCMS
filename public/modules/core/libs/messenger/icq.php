<?php

/**
 * Core
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: icq.php,v 1.2 2010/07/21 17:57:21 surg30n Exp $
 */
  
class tf_messenger_icq extends tf_messenger_generic {

    /** @var WebIcqLite */
    protected $_transport;
    
    private $_uid;
    private $_password;
    
    /**
    * @throws tf_messanger_exception
    * @param mixed $param
    */
    function _connect($param) {
        
        if (!function_exists('socket_create')) throw new tf_messanger_exception('ICQ: No sockets');   
        if (!function_exists('iconv')) throw new tf_messanger_exception('ICQ: No iconv');      
        
        if (!$this->_transport) {
            require_once "ext/icq/icq.php";
            $this->_transport = new WebIcqLite();
        }

        $return = $this->_transport->connect(
          $param[0] //'uin']
        , $param[1] //'password']
        );
        
        
        // UIN blocked, please try again 20 min later
        if (!$return) {
            throw new tf_messanger_exception($this->_transport->error);
        }

        return $return;
    }
    
    function _send($data) {
    
         $payload = $data['body'];  
         
         $payload = iconv('UTF-8', 'WINDOWS-1251', $payload);
        
         foreach ($this->_to as $t) {
             $this->_transport->send_message($t, $payload);
         }
    }
    
    function _close() {
        if ($this->_transport && $this->_connected)
        $this->_transport->disconnect();
    }

}