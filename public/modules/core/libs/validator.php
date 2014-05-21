<?php

/**
* @package core
* @version $Id: validator.php,v 1.4 2010/07/21 17:57:17 surg30n Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/    

/**
* Validator
*/           
class tf_validator {
    
    /**
    * Valid options for parse_str
    */
    private $_parse_options = array(
             'max_length' => 1
           , 'strip_tags' => 1 // = allowed_tags  or true if swap all tags
           , 'clean_html' => 1
           , 'sql_escape' => 1
           , 'clean_html' => 1
           , 'nl2br'      => 1
    );

    /**
    * Is email
    */
    function is_email($value) {
          if (empty($value) 
        || !preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $value)) return false;
            return true;
    }
    
    /****
    * Parse string 
    *
    * @param string input
    * @param mixed config (@see self::$_parse_options)
    */         
    function parse_str($str, $cfg) {
      
      // skip some shit     
      // $str = str_replace(array('  ',"'","`"),array(' ','"','"'),$str);
      
      // support for 1 param
      if (is_string($cfg)) $cfg = array($cfg => true);
      
      // check param is fine
      if (count(array_intersect_key($cfg, $this->_parse_options)) != count(array_keys($cfg))) {
          $cfg_string = array();
          foreach (($keys = array_keys($cfg) ) as $key) 
            if (!isset($this->_parse_options[$key])) $cfg_string[] = $key;
          $cfg_string = implode(', ', $cfg_string);
          throw new validator_exception('Unknown cfg params ' . $cfg_string . ' in validator::parse_str', validator_exception::ERROR);
      }
               
      // strip tags
      if (isset($cfg['strip_tags'])) {
        $cfg['allowed_tags']= (is_string($cfg['strip_tags']) ? $cfg['strip_tags'] : false);
        $str = strip_tags($str, $cfg['allowed_tags']);
      }

      // max length
      if (!empty($cfg['max_length'])) {
        $str = strings::substr($str, 0, $cfg['max_length']);          
      }

      // sql escape
      if (isset($cfg['sql_escape'])) {
        if (class_exists('sql_db', 0) ) {
            // @todo different database types support?
            $str = mysql_real_escape_string($str);
            // $str = sql_db::sql_escape($str);
        }
      }   

      // Replaces \n -> <br>      
      // This is tidy issure - it will break <br/> instead of <br>
      if (isset($cfg['nl2br'])) { 
          $str = str_replace("\n", "<br>", $str);
      }
      
      // Clean with tidy      
      if (isset($cfg['clean_html'])) { 
          $str = $this->clean_html($str);
      }

      return $str;
  }
  
  /**
  * Tidy configure
  */
  private $_tidy_config = array(
          "output-xhtml"    => true
        , "clean"           => true
        , "show-body-only"  => true
        , "drop-font-tags"  => true
  );  
  
  /**
  * Clean html
  */  
  function clean_html($code) {

      // script
      $code = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iU','$1=$2nojavascript...',$code);
      $code = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iU','$1=$2novbscript...',$code);
      
      // on-events
      $code = preg_replace('#(<[^>]+[\s\r\n\"\'])(on|xmlns)[^>]*>#iU', "$1>" , $code);

      // styles        
      $attribs = 'style'; // "at1|at2|at3"
      $code =  preg_replace('/[\s]+(' . $attribs . ')=[\s]*("[^"]*"|\'[^\']*\')/i', "", $code); //double quoted
      $code =  preg_replace('/[\s]+(' . $attribs . ')=[\s]*[^ |^>]*/i', "", $code);     //not quoted

      // strip other shit      
      $_whitelist = '
           <table><tbody><thead><tfoot><tr><th><td><colgroup><col>
           <p><br><hr><blockquote>
           <b><i><u><sub><sup><strong><em><tt><var>
           <code><pre><fieldset><legend>
           <a><img><h2><h3>
           <ul><ol><li><dl><dt>';
      
      $code = strip_tags($code, $_whitelist);
      
      if (class_exists('tidy')) {
          $tidy = new tidy;
          // @fixme Encoding hardcored
          $tidy->parseString($code, $this->_tidy_config, 'utf8');
          $tidy->cleanRepair();          
          $code = (string)$tidy;
      }
      
      return $code;
  }
  
  /***
  * @desc simply filter $_POST
  */
  
  function filter_post() {
      
      if (empty($_POST)) return false;
      
      foreach($_POST as $k => &$v) {
        $v = validator_c::parse_str($v,array('stript_tags'=>true,'sql_escape'=>true)); 
      }
      
  }

    
}
  
