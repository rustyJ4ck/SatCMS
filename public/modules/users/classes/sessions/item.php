<?php
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: item.php,v 1.2.2.2 2011/02/24 09:17:46 Vladimir Exp $
 */


/**
 * Class sessions_item
 * @property int id
 * @property int uip
 * @property int uid
 * @property string sid
 * @property string skey generated sessionKey
 * @property int last_update
 * @property string uip_string
 * @property string last_update_string
 */
class sessions_item extends abs_collection_item {

    /**
     * Change session data thru this var. @see self::get_sdata()
     * @var aregistry
     */
    protected $storage;

    /**
     * Build
     */
    function construct_after() {
        $sdata = empty($this->sdata) ? array() : unserialize($this->sdata);

        $this->storage = new aregistry($sdata);

        $this->uip_string               = !$this->uip ? 'no_ip' : long2ip($this->uip);
        $this->last_update_string       = !$this->last_update ? 'never' : date('d.m.Y H:i', $this->last_update);
    }

    function get_hash() {
        return $this->shash;
    }

    /**
     * @return aregistry mixed if key used
     */
    function get_storage($key = null) {
        return isset($key) ? $this->storage->get($key) : $this->storage;
    }

    /**
     * Sync session data to storage
     */
    function on_session_end() {

        $sdata = $this->storage->as_array();

        if (empty($sdata)) $sdata = ''; else $sdata = serialize($sdata);

        // if dirty, commit changes
        if ($this->sdata !== $sdata) {
            $this->sdata = $sdata;
            $this->update_fields('sdata');
        }
    }

    /**
    * Get sid?
    */
    function get_sid() {
        return $this->sid;
    }
    
    /**
    * Insert/update
    * needs: uid, uip
    */    
    function modify_before($data) {
        // generate skey
        $data['skey'] = $this->get_container()->get_session_key();        
    }     
    
    /**
    * Update last
    */
    function last_update() {             
        $this->last_update = time();
        $this->update_fields("last_update");
    }
    
    /**
    * Update uid
    */
    function update_uid($uid) {
        $this->uid = $uid;
        $this->update_fields("uid"); 
    }
    
    /*
    function render_before() {
        $this->uip = long2ip($this->uip);       
        $this->last_update_ = time() - (int)$this->last_update;
    }
    */
    

    function get_expire_time() {
        return $this->last_update + $this->get_container()->get_expire_time();
    }
    
}

/**
 * Dummy session
 */

class dummy_sessions_item extends sessions_item {

    public $id = -1;

    public $uip = '';
    public $uid = '-1';
    public $last_update = '';
    public $skey = '';
    public $shash = '';
    public $sid = '';
    public $sdata = array();

    protected $is_dummy = true;

    /**
     * Emulate constructor
     */
    function construct_dummy_after() {
        parent::construct_after();
    }


}