<?php

/**
* @package TwoFace
* @version $Id: request.php,v 1.6.2.2.2.9 2013/09/29 09:25:09 jack Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/    


/**
* Identification vars
*/                   
class ident_vars extends aregistry {

    /**
     * Normalize
     * @return array
     */
    function as_array() {

        $result = array();
        if (!empty($this->_registry))
        foreach ($this->_registry as $k => $v) {
            $result[$k] = $this->_cast($k, $v);
        }

        return $result;
    }

    private function _cast($id, $v) {
        return preg_match('@id$@', $id) ? (int)$v : (string)$v;
    }
    
    function get($id,  $default = null) {
         $return = parent::get($id);                     
         $return = !isset($return) ? $default : $return;
         return $this->_cast($id, $return);
    }       
}

/**
* Request
* @todo validate and normilize here
*/           
class tf_request extends singleton {
/** 
    * @static ident vars (registry)
    * usage thru @see self::get_params()
    */
    private static $_ident_vars;
    
    // ident tags
    
    /** m */        const IDENT_MODULE        = 'm';
    /** c */        const IDENT_CONTROLLER    = 'c';
    /** op */       const IDENT_ACTION        = 'op';
    /** id */       const IDENT_ID            = 'id';
    /** pid */      const IDENT_PID           = 'pid';  // parent id
    /** pid */      const IDENT_GID           = 'gid';  // group id
    /** do */       const IDENT_DO            = 'do';
    /** start */    const IDENT_START         = 'start';
                
    /**
    * All user contributed data
    */
    private $_all;
    private $_get;  
    private $_post;  
    private $_files;
    private $_cookies;
    
    private $_host;

    private $_uri;

    public $method;
              
    /**
    * Create request
    */
    function __construct() {

        $this->method = @$_SERVER['REQUEST_METHOD'];
        
        $this->_host = @$_SERVER['HTTP_HOST'];

        $this->_uri = @$_SERVER['REQUEST_URI'];
        
        $this->_post      = $_POST;
        $this->_get       = $_GET;
        $this->_files     = $_FILES;
		$this->_cookies   = $_COOKIE;
        
        $this->_normalize_files();
        
        $this->_all = functions::array_merge_recursive_distinct($_COOKIE, $this->_get);
        $this->_all = functions::array_merge_recursive_distinct($this->_all, $this->_post);
        $this->_all = functions::array_merge_recursive_distinct($this->_all, $this->_files);
        

         // @todo use self::TAG_ ..
        self::$_ident_vars = new ident_vars(
            array(
                  'id'        => $this->postget('id') //,       0)
                , 'pid'       => $this->postget('pid') //,      0)
                , 'gid'       => $this->postget('gid') //,      0)
                , 'c'         => $this->postget('c') //,        '')
                , 'op'        => $this->postget('op') //,       '')
                , 'do'        => $this->postget('do') //,       '')                 
                , 'type'      => $this->postget('type') //,     '')
                , 'embed'     => $this->postget('embed') //,    '')

    // not implemented
    //         , 'lang'      =>isset($_GET['lang']) ? $_GET['lang'] : ( isset($_POST['lang']) ? $_POST['lang']  : request_var('lang',$this->config['default_lang'])  ),

                , 'start'     => $this->postget('start') //, 0)
                , '2print'    => $this->postget('2print') //, '')

                , 'm'    =>  preg_replace('/[^\w\d]/', '', $this->postget('m', ''))
        ));
        
    }
    
    /*      
        efs[name][group-1][file0] = ''
        group1[name][file3] = ''
    */

    /**
    * Normilize php multidim files uploads:
    */
    function _normalize_files() {
        $new_files = array();
        if (empty($this->_files)) return;
        foreach ($this->_files as $kf => $vf) {
            $_vf = array();
            if (is_array($vf['name'])) {
                
                foreach($vf['name'] as $vfname_k => $vfname_v){           // group                

                    // [group][files]
                    if (!is_array($vfname_v)) {
                        $_vf [$vfname_k] = array(
                                'name'     => $vf['name'][$vfname_k],
                                'type'     => $vf['type'][$vfname_k],
                                'tmp_name' => $vf['tmp_name'][$vfname_k],
                                'error'    => $vf['error'][$vfname_k],
                                'size'     => $vf['size'][$vfname_k],
                            );
                    }
                    else {
                    // [group][subgroup][files]
                        foreach($vfname_v as $vfname_v2k => $vfname_v2v){
                            $_vf [$vfname_k][$vfname_v2k] = array(
                                'name'     => $vf['name'][$vfname_k][$vfname_v2k],
                                'type'     => $vf['type'][$vfname_k][$vfname_v2k],
                                'tmp_name' => $vf['tmp_name'][$vfname_k][$vfname_v2k],
                                'error'    => $vf['error'][$vfname_k][$vfname_v2k],
                                'size'     => $vf['size'][$vfname_k][$vfname_v2k],
                            );
                            
                            // var_dump($vf['name'][$vfname_k][$vfname_v2k]);
                        }
                    }
                }
            }
            else {
                // nop
                $_vf = $vf;
            }
            $new_files[$kf] = $_vf;
        }
        $this->_files = $new_files;
    }

    /**
     * @return mixed URI
     */
    function uri() {
        return $this->_uri;
    }
    
    /**
    * Get identification variable
    * use core::TAG_ constants for naming
    * @return mixed registry|mixed or single param
    */
    public static function get_ident($name = false, $default = null) {
        return ($name) ? self::$_ident_vars->get($name, $default) : self::$_ident_vars;
    }
    
    public static function set_ident($name, $value) {
        self::$_ident_vars->set($name, $value);
    }    
    
    /**
    * @return string HTTP_HOST
    */
    function get_host() {
        return $this->_host;
    }

    /**
    * Get POST & FILES    
    */
    function postfiles() {
        return functions::array_merge_recursive_distinct($this->_post, $this->_files);
    }
    
    /**
    * Get FILES+POST    
    * with POST priority
    */
    function filespost() {
        return functions::array_merge_recursive_distinct($this->_files, $this->_post);
    }    
    
    /**
    * Get POST
    */
    function get_post() {
        return $this->post();
    }

    /**
    * Get POST
    */
    function post($name = null, $default = null) {
        return $name ? (isset($this->_post[$name])?$this->_post[$name]:$default) : $this->_post;
    }

    /**
     * Check if something posted
     * @param null $name
     * @return bool
     */
    function has_post($name = null) {
        return ($name) ? array_key_exists($name, $this->_post) : !empty($this->_post);
    }
    
    /**
    * Get FILES
    */
    function get_files() {
        return $this->files();
    }
    
    /**
    * Get bulk data
    * @return array CGPF
    */
    function get_bulk() {
        return $this->all();
    }
    
    function get_postfiles() {
        return $this->postfiles();
    }

    /**
    * Get FILES
    */
    function files($name = null) {
        return $name ? @$this->_files[$name] : $this->_files;
    }
    
    /**
    * Get GET
    */
    function get($name = null, $default = null) {
        return $name ? (isset($this->_get[$name])?$this->_get[$name]:$default) : $this->_get;
    }

    /**
    * P/G
    */
    function postget($name = null, $default = null) {
        return ($a = $this->post($name, null))
            ? $a
            : $this->get($name, $default);        
    }
           
    function cookie($id, $default = null) {
        return isset($this->_cookies[$id]) ? $this->_cookies[$id] : $default;
    }                  
           
    /**
    * Get bulk data
    * @return array CGPF
    */
    function all($name = null, $default = null) {
        return $name ? (isset($this->_all[$name])?$this->_all[$name]:$default) : $this->_all;     
    }

    /**
     * Check token
     * @return bool
     */
    public function forged() {

        $token = $this->postget(tf_auth::CSRF_TOKEN);

        // nginx can remove this header
        $token = $token ?: @$_SERVER['HTTP_SC_CSRF_TOKEN'];

        //Request::header('X-CSRF-Token');
        //apache_request_headers();

        //dd($_SERVER, core::lib('auth')->token());

        return $token !== core::lib('auth')->token();
    }


}
