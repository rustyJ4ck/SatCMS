<?php
  
/**
* @package TwoFace
* @version $Id: iconv.php,v 1.1.2.1 2010/08/03 06:21:04 Vladimir Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/
  
class iconv {
       
    const UNICODE = 'UTF-8';
    private static $_charset = 'UTF-8';
        
    /** internal encoding */
    static function set_charset($c) {
        $self_charset = self::$_charset;
        self::$_charset = $c;                  
        return $self_charset;
    }
    
    /** cp1251->UTF */
    static function convert_to($input) {   
        if (empty($input)) return $input;    
        if (self::$_charset == self::UNICODE) return $input;
        if (is_array($input)) return self::convert_to_array($input);
        return (is_string($input)) ? iconv(str_replace('-', '', self::$_charset), self::UNICODE, $input) : $input;
    }

    /** utf8-cp1251 */
    static function convert_from($input) {
        if (empty($input)) return $input;
        if (self::$_charset == self::UNICODE) return $input;
        if (is_array($input)) return self::convert_from_array($input);
        $return = (is_string($input)) ? iconv(self::UNICODE, str_replace('-', '', self::$_charset), $input) : $input;
        
        if (!$return) {
             core::dprint(array('strings::convert_from fail %s', $input), core::E_DEBUG3); 
        }
        
        return $return;
    }

    // cp1251->UTF          (arrays)
    static function _convert_to(&$v, $k) {
            $v = self::convert_to($v);
    }

    // cp1251->UTF          (arrays)
    static function _convert_from(&$v, $k) {
            $v = self::convert_from($v);
    }    
    
    // -> UTF callback
    static function convert_to_array($input) {
        if (!is_array($input)) return false;
        reset($input);
        array_walk_recursive($input, "self::_convert_to");    
        return $input;
    }

    // -> CP callback
    static function convert_from_array($input) {
        if (!is_array($input)) return false;
        reset($input);
        array_walk_recursive($input, "self::_convert_from");    
        return $input;
    }
    
}