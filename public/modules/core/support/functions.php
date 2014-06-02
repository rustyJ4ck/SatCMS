<?php

/**
* @package TwoFace
* @version $Id: functions.php,v 1.6.2.1 2010/08/03 06:21:03 Vladimir Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/    

/**
 * functions
 */

class functions {
    
    /**
    * MD5 buster
    */
    static function hash($str) {
        return bin2hex(md5($str, true));
    }

    /**
     * Make hash
     * @param $post
     * @return string
     */
    static function url_hash($url = null, $length = null) {

        $base = 36;

        if (!$url) {
            $url = uniqid(rand(), true);
            // openssl_random_pseudo_bytes(32)
        }

        $crc = sprintf('%u', crc32($url));
        $hash = base_convert($crc, 10, $base);

        if ($length) {
            $hash = substr($hash, -1 * $length);
        }

        return $hash;
    }
    
    /**
    * set_var
    * Set variable, used by {@link self::request_var the request_var function}
    */
    protected static function set_var(&$result, $var, $type)
    {
        settype($var, $type);
        $result = $var;

        if ($type == 'string')
        {
            $result = trim(/*htmlspecialchars*/(str_replace(array("\r\n", "\r"), array("\n", "\n"), $result)));
            $result = stripslashes($result);
        }
    }

    /**
    * request_var
    * Used to get passed variable
    */
    public static function request_var($var_name, $default = '' /*, $filter_gpc = false*/)
    {
        if (!isset($_REQUEST[$var_name]) || (is_array($_REQUEST[$var_name]) && !is_array($default)) || (is_array($default) && !is_array($_REQUEST[$var_name])))
        {
            return (is_array($default)) ? array() : $default;
        }

        $var = $_REQUEST[$var_name];
        
        if (!is_array($default))
        {
            $type = gettype($default);
        }
        else
        {
            list($key_type, $type) = each($default);
            $type = gettype($type);
            $key_type = gettype($key_type);
        }

        if (is_array($var))
        {
            $_var = $var;
            $var = array();

            foreach ($_var as $k => $v)
            {
                if (is_array($v))
                {
                    foreach ($v as $_k => $_v)
                    {
                        self::set_var($k, $k, $key_type);
                        self::set_var($_k, $_k, $key_type);
                        self::set_var($var[$k][$_k], $_v, $type);
                    }
                }
                else
                {
                    self::set_var($k, $k, $key_type);
                    self::set_var($var[$k], $v, $type);
                }
            }
        }
        else
        {
            self::set_var($var, $var, $type);
        }

        return $var;
    }       
    
    /** 
    * Convert array to object 
    * @return object converted object
    */
    static function array2object($arg_array) {
    $tmp = new stdClass; // start off a new (empty) 
    foreach ($arg_array as $key => $value) {
        if (is_array($value)) { 
            // if its multi-dimentional
            $tmp->$key = self::array2object($value);
        } else {
            if (is_string($key)) { // can't do it with numbers
                $tmp->$key = $value;
            }                       
        }
    }
    return $tmp; 
    }

    /**
    * Приоритет у последнего массива
    */
    static function &array_merge_recursive_distinct(array &$array1, &$array2 = null)
    {
      $merged = $array1;
     
      if (is_array($array2))
        foreach ($array2 as $key => $val)
          if (is_array($array2[$key]))
            $merged[$key] = (isset($merged[$key]) && is_array($merged[$key])) ? self::array_merge_recursive_distinct($merged[$key], $array2[$key]) : $array2[$key];
          else
            $merged[$key] = $val;
     
      return $merged;
    }
    
    static function array_delete_by_val(&$a, $v) {
        $id = array_search($v, $a);
        if ($id !== false) unset($a[$id]);
    }


    /** pack */
    public static function pack_data($data) {
        return base64_encode(serialize($data));
    }

    /** unpack */
    public static function unpack_data($data) {
        return unserialize(base64_decode($data));
    } 
   
    /** redirect */
    public static function redirect($url) {
        header('Location: ' . $url);
    }
   
    /** translit */
    public static function translit($s, $allowed_syms = '') {
        $translit=array(
            '  '=>'_',
            ' '=>'_',
            'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j',
            'з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
            'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch',
            'ъ'=>"'",'ы'=>'i','ь'=>"'",'э'=>'е','ю'=>'yu','я'=>'ya','А'=>'A','Б'=>'B','В'=>'V','Г'=>'G',
            'Д'=>'D','Е'=>'E','Ё'=>'E','Ж'=>'J','З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M',
            'Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'TS',
            'Ч'=>'CH','Ш'=>'SH','Щ'=>'SCH' /*,'Ъ'=>"'"*/,'Ы'=>'I' /*,'Ь'=>"'"*/,'Э'=>'Е','Ю'=>'YU','Я'=>'YA',);
        
        
        $s = strings::str_replace(array_keys($translit), array_values($translit), $s);
                                                                     
        $allowed_syms = preg_quote($allowed_syms);
        $preg_exp = sprintf('~[^a-z\d_%s]~iU', $allowed_syms);
        
        $s = preg_replace($preg_exp, '', $s);
        return $s;
    }
    
    /*
    public static function translit($s)    {
        $s = strtr($s
            , "АБВГДЕЗИЙКЛМНОПРСТУФЦЪЫЬабвгдезийклмнопрстуфцъыь"
            , "ABVGDEZIJKLMNOPRSTUFC\"Y'abvgdezijklmnoprstufc\"y'");
        $doit = array(' ' => '_', 'Ё'=>'JO','Ж'=>'ZH','Х'=>'KH','Ч'=>'CH','Ш'=>'SH','Щ'=>'SHCH','Э'=>'EH','Ю'=>'JU','Я'=>'JA','ё'=>'jo','ж'=>'zh','х'=>'kh','ч'=>'ch','ш'=>'sh','щ'=>'shch','э'=>'eh','ю'=>'ju','я'=>'ja');
        $s = strtr($s, $doit);
        $s = preg_replace('~[^a-z\d_]~iU', '', $s);
        return $s;
    }
    */
    
    /**
    * Disable http cache
    * (usercp-admin)
    */
    public static function headers_no_cache() {
        if (!empty($HTTP_SERVER_VARS['SERVER_SOFTWARE']) && strstr($HTTP_SERVER_VARS['SERVER_SOFTWARE'], 'Apache/2'))
            header ('Cache-Control: no-cache, pre-check=0, post-check=0');
        else
            header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');

        //  @header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
        header ('Expires: 0');
        header ('Pragma: no-cache');
    }


    /**
     * Fix UTF escaping in php<5.4
     *
     * @param mixed $arr
     */
    static function json_encode($arr) {

        // @todo fix this
        if (!is_array($arr)) {
            return json_encode($arr);
        }

        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return json_encode($arr, JSON_UNESCAPED_UNICODE);
        }

        // convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127).
        // So such characters are being "hidden" from normal json_encoding
        array_walk_recursive($arr, function (&$item, $key) {
            if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
        });

        return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
    }

    /** fix conflict with __call */
    public static function is_callable($method) {

        if (is_string($method) || is_object($method)) {
            return is_callable($method);
        }
        else
            if (is_array($method)) {
                return method_exists($method[0], $method[1]) && is_callable($method);
            }

        return false;
    }

    
}

