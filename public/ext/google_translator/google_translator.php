<?php

/**
* Google translator lib
* 
* @package    SatCMS
* @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
* @copyright  SurSoft (C) 2008
* @version    $Id: google_translator.php,v 1.4 2010/02/01 15:00:48 surg30n Exp $
* 
* <code>
* $g = new Google_Translator();
* $g->set_langs("en", "ru")
* ->set_text($whut)
* ->translate();
* </code>
*/   

class google_translator {
    
    
    private $_user_agent = "Mozilla/5.0 (Windows; U; Windows NT 6.0; ru-RU; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)";
            
    private $_sleep = 0;

    private $_lang_s = '';
    private $_lang_t = '';

    private $_sources = array();

    function set_sleep($s) {
        $this->_sleep = $s;
        return $this;
    }

    function set_langs($from, $to) {
        $this->_lang_s = $from;
        $this->_lang_t = $to;
        return $this;
    }

    /**
    * @param mixed string|array of strings
    */
    function set_text($src) {
    $this->_sources = is_array($src) ? $src : array($src);
    return $this;
    }

    /**
    * http://translate.google.com/translate_a/t?client=t&text=where%20is%20the%20fucking%20mr.%20bin&sl=en&tl=ru
    */

    /**
    * @param array text
    * @return array Warn, [0]=>string! 
    * return false if sources is empty!
    */

    function translate() {

      if (empty($this->_sources)) return false;
        
      $text = $this->_sources;   
      $result = array();    
            
        $url = "http://translate.google.com/translate_t";

        $post = array();
        $post['langpair'] = $this->_lang_s . "|" . $this->_lang_t;

          foreach ($text as $k => $t) {   
              if (!empty($t)) {
                $post['text'] = $t;
                 $out = $this->wget($url,$post);
                 $this->l_cut($out, '<div id=result_box dir="ltr">');
                 $this->r_cut($out, '</div>');
                 
                 $out = htmlspecialchars_decode($out);
                 $out = str_replace(array('</ ', '< '), array('</', '<'), $out);
                 
                 //$out = preg_replace("#\x0D\x0A#u", "\x0D", $out);  
//                 $out = preg_replace("#\x0A#u", '', $out);  
//                 $out = preg_replace("#\x0D#u", "\x0D\x0A", $out);  
                 $out = preg_replace("#(\x0D|\x0A)<br>\s#u", "", $out);  
                 
                 // blockquote?
                 $out = preg_replace('#</Цитата>#u', '</blockquote>', $out);  
                 
                 
                 $result[$k] = $out;
                                  
                 if ($this->_sleep) sleep($this->_sleep);      
              }
          } 
          
        return $result;
    }

    /**
    * filt google html
    */
    function js_stripslashes($str) {

        $replace = array(
            "\n"    , "\r"    , '"'     ,
            "'"     , "&"     , "<"     , ">"       , '</'      , '<'
        );
        $pattern = array(
            '\n'    , '\r'    , '\"'    ,
            "\\'"   , '\x26'  , '\x3c'  , '\x3e'    , '</ '     , '< '
        );
        
      
      
        return str_replace($pattern, $replace, $str);
      
    }    

    /*
    // translate a given URL from and to the selecting language
    function translate_URL($url) {
        
        if ( empty($url) ) {            
            $this->through_error('No URL specified.');
            return false;
        } elseif(!eregi("^(https?://)?(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-z_!~*'()-]+\.)*([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.[a-z]{2,6})(:[0-9]{1,4})?((/?)|(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$",$url)) {                    
            $this->through_error('Not a valid URL format.');
            return false;
        }
    
        $this->validate_langPair();
        if ( $this->check_continue()===false ) return false; 

        $langpair = $this->langFrom."|".$this->langTo;        
        
        $url='http://66.102.9.104/translate_c?hl=ro&safe=off&ie=UTF-8&oe=UTF-8&prev=%2Flanguage_tools&langpair='.$langpair.'&u='.urlencode($url);        
        $RawHTML = $this->getData_Curl($url);        
        
        return $RawHTML;
    }
    */


    private
    function wget($url, $post = false){

        if( !extension_loaded('curl') ){
                $this->through_error('You need to load/activate the cURL extension (http://www.php.net/cURL).');
                return false;
        }

        $ch = curl_init(); // init curl

        $Headers = array(
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3",
            "Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7"        
        );                                                   
        curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
 
        curl_setopt($ch, CURLOPT_REFERER, "http://translate.google.com/");
        curl_setopt ($ch, CURLOPT_USERAGENT, $this->_user_agent); 

        curl_setopt($ch, CURLOPT_COOKIEJAR,"cookie");
        curl_setopt($ch, CURLOPT_COOKIEFILE,"cookie");        
        curl_setopt($ch, CURLOPT_URL, $url); // set the url to fetch
        curl_setopt($ch, CURLOPT_HEADER, 0); // set headers (0 = no headers in result)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // type of transfer (1 = to string)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // time to wait in 
        curl_setopt($ch, CURLOPT_POST, 0);
        
        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));        
        }    

        $content = curl_exec($ch); // make the call

        curl_close($ch); // close the connection       
        
        return $content;
    }        

    private
    function l_cut(&$text,$search,$offset=0){
        $pos=strpos( $text, $search );    
        if ($pos === false) { $subtext=$text; }
        else { 
            $pos1=$pos+strlen($search)+$offset;
            $subtext=substr($text, $pos1 );
        }    
        $text = $subtext;
    }    

    private
    function r_cut(&$text,$search){
        $pos=strpos ( $text, $search );
        $text = ($pos === false ) 
            ? $text
            : substr($text, 0 ,$pos );
    }    
 
} 
