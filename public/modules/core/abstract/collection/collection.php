<?php
/**
 * Items Collection
 *
 * Load modificators (construct):
 * ------------------------------
 *  no_preload                      // do not load anything
 *  no_extra                        // without extrafields
 *  no_dependencies                 // without depend objects
 *
 *  load_only_id                    // eq where_sql = "id = {$id}"
 *  order_sql
 *  where_sql
 *  join_sql | join_where_sql
 *  limit_sql = array(0,1)          // offset, count
 *  full_load                       // for auto load_secondary on startup
 *  table                           // map sql table
 *  tpl_table                       // map tpl var
 *  key                             // make 'key' (othrwise "id" used) for items array
 *  render_by_key                   // id<->key rendering
 *
 *  in_editor (bool)                // set editor mode
 *
 * Valid fields types:
 * -------------------
 *   list                           // array('type'=>'list', 'values'=>array('usd'=>'usd', 'eur'=>'eur'))
 *   text
 *       format = params to tf_validator // default is strip_tags
 *       no_format= true
 *       size = "size, characters"
 *   numeric
 *       float = "false"
 *       size = "size, bites"
 *       unsigned = "false"
 *       long = "false"                  // for ip addr
 *
 *       currency // number_format($fld, [0],  [1],  [2]);
 *                // default  number_format($fld, 2, ',', '.');
 *
 *   unixtime
 *       no_check = true             // no check on modify
 *       format = input format for strings
 *
 *   virtual                        // internal usage fields
 *                                  // 'method' => 'name' calls: virtual_{name}
 *
 *   position                       // position field
 *       space = "field"            // namespace for position (belong to pids), autofill when new object created
 *
 *   file                           // create = array('tmp_name', 'name', 'size');
 *   image
 *       thumbnail       = "10"|"10%" (string) OR array(x,y)
 *                         or  (width,height,filter(id,params))              id=1, IMG_FILTER_GRAYSCALE
 *       original        = false save original file in [original]
 *       replace         = "false"
 *       storage         = uploads/"images/avatars"
 *       width & height  = "10" (px/%)
 *       max_width       =
 *       max_height      =
 *       allow           = array('gif', 'jpg' ...)
 *       watermark       = array('file' => 'from/root/wm.png', 'options' => array('opacity' => 50, x,y,opacity,min_width,min_height))
 *
 *       spacing         = N generate n/ for the storage optimize
 *
 *   boolean
 *
 *  SCHEME
 *  ------
 *    indexes = (name => id|id1,id2,...,idN)
 *
 *  KEYS
 *  -----
 *  key = true, unique => true, autoincrement = true (default for ID), index = name
 *
 *   render => (
 *       format
 *   )
 *
 *
 * Valid fields modificators:
 * --------------------------
 *   autosave    = bool              // saves value when object updates
 *   default     = value             // default value for new objects ('now' - current time)
 *   make_seo    = true|field_name   // autotranslit and check for bad names. takes initial value from field_name
 *      space = "field"              // namespace for make seo
 *      [key, translit]
 *      ['key' => string, 'translit' => bool, 'strict' => bool]
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

/**
 * Collection interface
 * @package core
 *
 * use @see strings
 */
interface IAbs_collection {
}

/**
 * Class collection_params
 */
class collection_params extends registry {
}

/**
 * Class collection_formats
 */
class collection_formats extends aregistry {
}

/**
 * Elements collection
 * @package core
 */
abstract class abs_collection implements IAbs_Collection, IteratorAggregate {

    const VF_FILE = 'model.php';

    /** @var array cached vf from file [domain] */
    private static $_vf_file = array();

    /** @var  string /root/ */
    protected $_root;

    protected $vfs_prototypes = array(
      'list'   => array()
    , 'numeric'  => array()
    , 'text'     => array()
    , 'boolean'  => array()
    , 'unixtime' => array()
    , 'virtual'  => array()
    , 'file'     => array()
    , 'image'    => array()
    , 'position' => array()
    , 'relation' => array()
    );


    protected $readonly = false;

    /** @var array(abs_collection_item)  */
    protected $items = array();

    protected $behaviors;

    /**
     * @var collection_params
     */
    public $config;

    /**
     * Join operations main table domain
     */
    const TABLE_DOMAIN = 'p1';

    /**
     * Class domain, need for linking
     * extra fields.
     * Fill it with lowercase!
     */
    protected $DOMAIN = false;
    protected $_class = false;

    /** example users_users */
    protected $_name;

    /** binded module */
    protected $_module_name;

    /**
     * Valid item fields
     */
    protected $fields;
    protected $fields_keys;

    /** backed when formats applied */
    protected $original_fields;

    /**
     * Extra fields storage
     * ARRAY('domain', 'storage_collection_class')
     * of false if disabled
     */
    protected $_with_extra_fields = false;

    /**
     * item class, setup automaticaly
     * @access private
     */
    private $item_class = "";

    /**
     * @var core
     */
    protected $core;

    /**
     * @var dbal
     */
    protected $db;

    /**
     * Ids cache
     */
    private $_ids_cache;

    /** last modified id */
    private $_last_id;

    /** last sql query (@see build_sql) */
    private $_last_query;

    /** @var int ctypeID  */
    protected   $_ctype_id;

    /** @var string ctype-string */
    private     $_ctype;

    /** @var  ctype_item */
    protected   $ctype;

    /**
     * With Dependencies?
     * @see self::with_deps()
     */
    private $_with_deps = false;

    /**
     * Delayed insert/update
     *
     * Warning! Delayed INSERTS doent not return new item IDs!
     * Use it with caution when creation items with dependencies
     */
    private $_delayed = false;

    /**
     * With positioning flag
     * @see self::with_positions()
     */
    private $_with_positions = false;

    /** initial order, for clear */
    private $_order_sql;

    /** set, inherited to items */
    protected $working_fields = array();

    /** key|id */
    protected $_key = '';

    /** @var string render2edt prefix */
    protected $tpl_prefix = 'tpl_';

    protected $formats;

    /**
     * Constructor
     * @param array configuration array
     */
    function __construct($config) {

        $this->config = new collection_params($this->config ? $this->config : null);
        $this->formats = new collection_formats($this->formats ? $this->formats : null);

        // default doamin
        if (!$this->DOMAIN) $this->DOMAIN = strtolower(substr(get_class($this), 0, -1 * strlen('_collection')));

        $class = get_class($this);
        if ($class == __CLASS__ && isset($config['class'])) {
            $class = $config['class'];
        }

        if ($class == __CLASS__) {
            throw new collection_exception('Aliases must set "class" param to collection |' . $class);
        }

        // default domain (like: users\users)
        if (!$this->_class) $this->_class =
            strtolower(substr($class, 0, -1 * strlen('_collection')));

        // pull objects
        $this->core  = core::get_instance();
        $this->db    = db_loader::get($config['connection']?:null);
        $this->_root = $config['root'];

        //
        // external configuration (model.php)
        //
        if (!isset($this->fields)) {
            $model_file = $this->_root . self::VF_FILE;

            $deb_01     = $class; //isset(self::$_vf_file[$this->DOMAIN]);
            $ext_config = array();

            if (!isset(self::$_vf_file[$class])) {
                if (file_exists($model_file)) {
                    $ext_config             = require $model_file;
                    self::$_vf_file[$class] = $ext_config;
                }
            } else
                $ext_config = self::$_vf_file[$class];

            $this->fields = array();

            // multiconfig model {fields, config}

            if (isset($ext_config['fields'])) {
                $this->fields = $ext_config['fields'];

                if (!empty($ext_config['config'])) {
                    $config = array_merge($config, $ext_config['config']);
                }

                if (!empty($ext_config['formats'])) {
                    $this->formats->from_array($ext_config['formats']);
                }

            } else {
                // just fields
                $this->fields = $ext_config;
            }

            // allow external data
            if (isset($ext_config['data']) && !isset($config['data'])) {
                $config['data'] = $ext_config['data'];
            }

            // behaviors
            if (isset($ext_config['behaviors']) && empty($this->behaviors)) {
                $this->behaviors = $ext_config['behaviors'];
            }

        }

        if (empty($this->fields)) {
            throw new collection_exception('Empty valid fields in ' . get_class($this));
        }

        // attach behaviors
        $this->create_behaviors();

        $this->construct_before($config);

        // update keys
        $this->prepare_fields();

        // key override
        if (!empty($config['key'])) {
            $this->set_key($config['key']);
        }

        if (empty($this->_key)) {
            if (($idvf = $this->get_field('id')) !== false) {
                $this->set_key('id'); // legacy
                $this->is_key_autoincrement(true);
            }
        }

        if ($m_class = @$config['item_class']) {
            // model class hack (get_class($this) dont work when class_alias used)
            $this->item_class = $m_class;
        }

        if (empty($this->_name)) {
            if (isset($config['name'])) $this->_name = $config['name'];
            else $this->_name = str_replace('\\', '_', $this->_class);
        }

        // auto assign items class (like class_collection)
        if (empty($this->item_class)) {
            $sz_cl            = $class;
            $sz_cl            = str_replace('collection', 'item', $sz_cl);
            $this->item_class = $sz_cl;
        }

        // positioning
        if ($this->with_positions() && empty($config['order_sql']))
            $config['order_sql'] = 'position';

        if (!isset($config['prefix']))
            $config['prefix'] = $this->db->get_prefix();

        if (!empty($config['table']))
            $config['table'] = $config['prefix'] . $config['table'];
        else
            $this->readonly = true;

        // use class name?
        $config['table'] = str_replace('%class%', $this->get_name(), $config['table']);

        /* по-умолчанию включаем сортировку по ID в обратном порядке
           (для лимита больше 1)
        */

        if (!isset($config['order_sql']) && $this->get_key() && $this->is_key_autoincrement() && (empty($config['limit_sql']) || 2 < $config['limit_sql'][1])) {
            $config['order_sql'] = $this->get_key() . ' DESC';
        }

        if (empty($this->item_class) || !class_exists($this->item_class, 0)) {
            throw new collection_exception('Cannot instanciate class ' . $this->item_class);
        }

        // WITHOUT EXTRA
        if (isset($config['no_extra'])) {
            $this->disable_extra_fields();
        }

        $this->check_config($config);

        $this->config->from_array($config);

        // old compat
        if ($rbk = $this->config->get('render_by_key')) {
            $this->is_render_by_key($rbk);
        }

        // extra fields
        if ($this->with_extra_fields()) {
            $this->load_extra_fields();
        }

        if (!empty($config['load'])) {
            $this->load();
        }

        if (!empty($config['data'])) {
            $this->load_from_array($config['data']);
        }

        $this->_order_sql = $this->get_order();

        // save original order
        if (empty($this->_order_sql)) $this->_order_sql = $this->get_order();
        else $this->set_order($this->_order_sql);

        if (class_exists('core', 0)) {
            $this->_ctype_id = ($this->ctype = $this->core->get_ctype($this->_get_ctype(), 1)) ? $this->ctype->get_id() : false;
        }

        $this->construct_after();
    }

    function construct_before(&$config) {}
    function construct_after() {}

    function set_key($k) {
        $this->_key = $k;
    }

    function get_key() {
        return $this->_key;
    }

    function is_key_autoincrement($fl = null) {
        if (null === $fl) return ($this->_key) ? @($this->fields[$this->_key]['autoincrement']) : false;

        if (!$this->_key) throw new collection_exception('autoincrement without key');
        $this->fields[$this->_key]['autoincrement'] = true;

        return $this;

    }

    /* Required definition of interface IteratorAggregate */
    public function getIterator() {
        return new collection_iterator($this->items);
    }

    /**
     * Get core handle for collection
     * Access to core from items thru this handle
     */
    public function get_core() {
        return $this->core;
    }

    /**
     * Used by childs
     * @depricated use connection()
     */
    public function get_db() {
        return $this->db;
    }

    /**
     * @param null $connection
     * @return dbal
     */
    function connection($connection = null) {
        if (isset($init)) {
            $this->db = $connection;
            return $this;
        }
        return $this->db;
    }

    /**
     * Set db (for test)
     */
    public function set_db($db) {
        $this->db = $db;
    }

    /**
     * Append behaviors in child class
     */
    function create_behaviors() {
    }

    function get_behaviors() {
        return $this->behaviors;
    }

    /**
     * Set/get delayed insert flag
     */
    public function is_delayed($set = null) {
        if (null !== $set) $this->_delayed = (bool)$set;

        return $this->_delayed;
    }

    /**
     * clone items fix
     */
    function __clone() {
        if ($this->count()) {
            $new_items = array();
            foreach ($this->items as $k => $item) {
                $new_items[$k] = clone($item);
                $new_items[$k]->set_container($this);
            }
            $this->items = $new_items;
        }
    }

    function get_formats() {
        return $this->formats;
    }

    /**
     * Check format 9autocheck for default
     * @param $id
     * @return bool
     */
    function has_format($id) {

        if (!$this->formats->count()) return false;

        // check default
        $defaultID = $id . '.default';

        $format = $this->formats->is_set($defaultID);

        if (!$format) {
            $format = $this->formats->is_set($id);
        }

        return $format ? true : false;
    }

    /**
     * Get format, like editor.form
     * @param $id
     */
    function get_format($id) {

        if (!$this->formats->count()) return array();

        // check default
        $defaultID = $id . '.default';

        $format = $this->formats->get($defaultID);

        if (is_null($format)) {
            $format = $this->formats->get($id);
        }

        return $format ? $format : array();

    }

    /**
     * site|editor|editor.(form|list)
     * @param $formatID
     */
    function set_format($formatID = null) {

        if (!isset($formatID)) {
            if ($this->original_fields) {
                $this->set_vfs($this->original_fields);
            }
            return $this;
        }
        else
        if ($format = $this->get_format($formatID)) {

            $current_format = $this->_backup_fields();
            $new_vfs = functions::array_merge_recursive_distinct($current_format, $format);

            // mix
            $this->set_vfs($new_vfs);

        }

        return $this;
    }

    /**
     * @return array backed
     */
    private function _backup_fields() {
        if (!isset($this->original_fields)) {
            $this->original_fields = $this->fields;
        }
        return $this->original_fields;
    }

    /**
     * @return collection_params
     */
    function get_config() {
        return $this->config;
    }

    /**
     * Set parameters
     * @param $method
     * @return $this
     */
    function configure($method) {

        if (is_array($method)) {
            $this->config->merge($method);
        } elseif ($method instanceof Closure) {
            $method($this->config);
        }

        return $this;
    }

    /**
     * @return collection_filter
     */
    function get_filter($base_url = null) {
        return new collection_filter($this, $base_url);
    }

    /**
     * Get domain (module/class)
     */
    public function get_class($short = false) {
        return get_class($this);
    }

    /**
     * Get domain
     */
    public function get_domain() {
        return $this->DOMAIN;
    }

    /**
     * Set items class
     */
    protected function set_item_class($class) {
        $this->item_class = $class;
    }

    /**
     * Get items class
     * @return mixed|string
     */
    function get_item_class() {
        return $this->item_class;
    }

    /**
     * Get vfs
     */
    public function get_vfs() {
        return $this->fields;
    }

    /**
     * Set vf
     */
    public function set_vfs($vfs) {
        $this->fields = $vfs;
        $this->prepare_fields();
    }

    /**
     * Update(&$vfs)
     * @param callable $callback
     * @param bool $prepare
     */
    function update_vfs(Closure $callback, $prepare = false) {

        $callback($this->fields);

        if ($prepare) {
            $this->prepare_fields();
        }
    }

    /**
     * Cook model fields
     */
    protected function prepare_fields() {

        foreach ($this->fields as $k => &$v) {

            $type = $v['type'];

            if (!isset($this->vfs_prototypes[$type])) {
                // bad model format
                throw new collection_exception('Invalid VF type used ' . $k . '@' . $type);
            }

            // position fix autosave
            if ($type == 'position') {
                $v['autosave'] = true;
                $this->with_positions(true);
            }

            // default image types
            if ($type == 'image') {
                if (!isset($v['allow'])) {
                    $v['allow'] = array('jpg', 'png', 'gif');
                }
            }

            if (!empty($v['key'])) $this->set_key($k);
        }

        $this->update_fields_keys();
    }

    /**
     * Update vfs keys
     */
    private function update_fields_keys() {
        $this->fields_keys = array_keys($this->fields);
    }

    /**
     * Get valid fields kets
     */
    public function get_fields_keys() {
        return $this->fields_keys;
    }

    /**
     * Append vf
     */
    public function append_field($key, $type, $params) {
        $this->fields[$key] = array_merge(array('type' => $type), $params);
    }

    /**
     * Get field
     * @param $name
     * @return bool|mixed
     */
    function field($name) {
        return $this->get_field($name);
    }

    /**
     * Get single valid field | or its property
     */
    public function get_field($vf, $key = null) {
        $value = isset($this->fields[$vf]) ? $this->fields[$vf] : false;

        if ($value && $key) {
            $value = array_get($value, $key);
        }

        return $value;
    }

    /**
     * Has field in model
     * @return bool
     */
    function has_field($key) {
        return isset($this->fields[$key]);
    }

    /**
     * Get fields
     * @return mixed
     */
    function fields() {
        return $this->fields;
    }

    /**
     * @deprecated use fields()
     * @see fields()
     * @return mixed
     */
    function get_fields() {
        return $this->fields;
    }

    /**
     * Normalize config
     */
    function check_config(&$config) {
        if (isset($config['sql_order'])) core::dprint('config typo: sql_order');
        if (isset($config['sql_where'])) core::dprint('config typo: sql_where');
    }

    /**
     * set_where
     * @param mixed string where sql or sprintf("sql", v1, v2, v3) syntax
     */
    function set_where( /*...$where*/) {
        $count = func_num_args();
        if (empty($count)) {
            $this->config->clear('where_sql');
        } else {
            $args  = func_get_args();
            $where = array_shift($args);
            if ($count > 1) {
                $where = vsprintf($where, $args);
            }

            $this->config->set('where_sql', $where);
        }

        return $this;
    }

    /**
     * Where {key}
     * @param $key
     * @param $value
     * @return $this
     */
    function where($key, $value, $operator = '=', $connector = 'AND', $raw = false) {
        return $this->append_where_vf($key, $value, $operator, $connector, $raw);
    }

    /**
     * @deprecated use where()
     * Append where from string
     * @return $this
     */
    function append_where_vf($key, $value, $operator = '=', $connector = 'AND', $raw = false) {

        $vf = $this->get_field($key);

        if (!$vf) {
            throw new collection_exception('Append_where invalid VF');
        }

        $key = static::TABLE_DOMAIN . '.' . $key;

        if (!$raw) {

            $value = $this->format_field_sql($key, $value);

            $where_sql = $key . " {$operator} " . $value;

            // boolean type
            if ('boolean' == $vf['type']) {
                $where_sql = ($value ? '' : 'NOT ') . $key;
            }

            // skip empty unixtime
            if ('unixtime' == $vf['type'] && empty($value)) {
                $where_sql = '';
            }

        } else {
            $where_sql = $key . ' ' . $value;
        }

        if (!empty($where_sql)) {
            $original_where = $this->get_where();

            if (empty($original_where)) {
                $this->set_where($where_sql);
            }
            else {
                $this->set_where($original_where . " {$connector} " . $where_sql);
            }
        }

        return $this;
    }

    /**
     * @param mixed $sql
     * @param mixed $op
     * @return self
     */
    function append_where($sql, $connector = 'AND') {
        if (empty($sql)) return $this;

        if (is_array($sql)) $sql = vsprintf($sql[0], array_slice($sql, 1));
        $where = $this->get_where();
        $where .= ((!empty($where) ? " {$connector} " : '') . $sql);
        $this->set_where($where);

        return $this;
    }

    /**
     * Get where
     */
    function get_where() {
        return $this->config->get('where_sql');
    }

    /**
     * set_order
     * @param mixed string where sql or sprintf syntax
     * @return $this
     */
    function set_order( /*$order*/) {
        $count = func_num_args();
        if (empty($count)) return false;
        $args  = func_get_args();
        $order = array_shift($args);
        if ($count > 1)
            $order = vsprintf($order, $args);
        $this->config->set('order_sql', $order);

        return $this;
    }

    function get_order() {
        return $this->config->get('order_sql');
    }

    function disable_order() {
        $this->set_order(false);

        return $this;
    }

    /**
     * Random
     */
    function set_random_order() {
        // mysql
        $this->config->set('order_sql', 'rand()');

        return $this;
    }

    /**
     * set_limit
     * @param integer count
     * @param integer offset
     * @return $this
     */
    function set_limit($count, $from = 0) {
        if (!$count)
            $this->config->clear('limit_sql');
        else
            $this->config->set('limit_sql', array($from, $count));

        return $this;
    }


    /**
     * Set join sql
     * INNER JOIN table p2 ON p1.id = p2.id
     * @todo refactor
     */
    function set_join($sql, $join_type = 'INNER JOIN') {
        if (is_array($sql)) $sql = vsprintf($sql[0], array_slice($sql, 1));
        $this->config->set('join_sql', $join_type . ' ' . $sql);

        return $this;
    }

    /**
     * Set join sql
     * INNER JOIN table p2 ON p1.id = p2.id
     * @todo refactor
     */
    function append_join($sql, $join_type = 'INNER JOIN') {
        if (is_array($sql)) $sql = vsprintf($sql[0], array_slice($sql, 1));
        $old_join = $this->config->get('join_sql');
        $old_join = $old_join ? "{$old_join} " : '';
        $this->config->set('join_sql', $old_join . $join_type . ' ' . $sql);

        return $this;
    }

    /**
     * Set join where
     * @todo refactor
     */
    function set_join_where($sql) {
        $count = func_num_args();
        if (empty($count)) {
            $this->config->clear('join_where_sql');
        } else {
            $args  = func_get_args();
            $where = array_shift($args);
            if ($count > 1) $where = vsprintf($where, $args);
            $this->config->set('join_where_sql', $where);
        }

        return $this;
    }

    /**
     * Build query
     * Assume first table is "p1", so next is p2..
     */
    function build_sql($for_count = false) {

        $where_sql      = $this->config->get('where_sql');
        $limits         = $this->config->get('limit_sql'); // as array (0,1)
        $join_sql       = $this->config->get('join_sql');
        $join_where_sql = $this->config->get('join_where_sql');
        $order_sql      = $this->config->get('order_sql');

        //if ($limits == array(0,1)) {
        // single row, so disable ordering
        //  $order_sql = '';
        //}

        if (!empty($join_where_sql))
            $where_sql = empty($where_sql) ? $join_where_sql : $where_sql . ' AND ' . $join_where_sql;

        // single item
        if ($onlyID = $this->config->get('load_only_id')) {
            // @todo sql-clean
            $onlyID    = is_numeric($onlyID) ? intval($onlyID) : "'" . core::lib('validator')->parse_str($onlyID, array('sql_escape' => 1)) . "'";
            $where_sql = (static::TABLE_DOMAIN . '.' . $this->get_key() . " = {$onlyID}");
            $limits    = array(0, 1);
            $order_sql = false;
        }


        if ($for_count) {
            $sql = "SELECT count(*) as s_count FROM " . $this->config->table . ' ' . static::TABLE_DOMAIN . ' '
                . (empty($join_sql) ? '' : "{$join_sql} ")
                . (!empty($where_sql) ? " WHERE {$where_sql} " : '');
        } else {

            $limits_sql = '';

            if (!empty($limits)) {
                $limits_sql .= " LIMIT {$limits[0]}";
                if (isset($limits[1]))
                    $limits_sql .= ",{$limits[1]}";
            }

            $fields_sql = static::TABLE_DOMAIN . ".*";

            if (!empty($this->working_fields)) {
                $fields_sql = array();
                $wfs        = $this->working_fields;
                array_unshift($wfs, $this->get_key());
                foreach ($wfs as $wfk) {
                    if (isset($this->fields[$wfk]) && $this->fields[$wfk]['type'] != 'virtual')
                        $fields_sql [] = static::TABLE_DOMAIN . '.' . $wfk;
                }
                $fields_sql = implode(', ', $fields_sql);
            }

            $sql = "SELECT " . $fields_sql . " FROM " . $this->config->table . ' ' . static::TABLE_DOMAIN . ' '
                . (empty($join_sql) ? '' : "{$join_sql} ")
                . (!empty($where_sql) ? " WHERE {$where_sql} " : '')
                . (!empty($order_sql) ? " ORDER BY {$order_sql} " : '')
                . (!empty($limits_sql) ? " {$limits_sql};" : ';');
        }

        $this->_last_query = $sql;

        return $sql;
    }

    /**
     * Get last builded query
     */
    function get_last_query() {
        return $this->_last_query;
    }


    /**
     * Reload (loaded) items data
     * @return abs_collection
     */
    function refresh() {
        foreach ($this->items as $i) {
            $i->load();
        }

        return $this;
    }

    /**
     * Mod from array
     * If items exists, load them before this point
     */
    function modify_from_array($data) {
        foreach ($data as $item) {
            $id = $this->get_key();
            $this->modify($item, @$item[$id]);
        }
    }

    /**
     * Load data from array
     */
    function load_from_array($data) {

        $only_id = $this->config->get('load_only_id');

        $key         = $this->get_key();
        $this->items = array();

        if (!empty($data))
            foreach ($data as $row) {

                if (!$only_id || ($only_id && $key && $row[$key] == $only_id)) {

                    $tmp = $this->alloc($row);

                    //id for class
                    $id = $key ? $row[$key] : false;
                    $this->append($tmp, $id);

                }
            }

        $this->load_after();

        return $this;
    }

    /**
     * Load first item
     * @return abs_collection_item
     */
    function load_first() {
        return $this->set_limit(1)
            ->load()
            ->get_item();
    }

    /**
     * Load colletction
     * All options found in ->config
     *
     * @param string|false specify force sql if need one
     *     otherwise build_sql is used
     *
     * @return $this
     */
    function load($_sql = false) {

        $this->items = array();

        // if empty table,
        if (!$this->config->table) {
            $data = $this->config->get('data');
            if (!empty($data)) $this->load_from_array($data);

            return $this;
        }

        $key = $this->get_key();
        $sql = $_sql ? $_sql : $this->build_sql();

        $res = $this->db->query($sql);

        while ($row = $this->db->fetch_row($res)) {

            $tmp = $this->_alloc($row, true);

            //id for class
            $id = $key ? $row[$key] : false;

            /*
            if ($tmp->fields[$key]['type']=='timestamp') {
              $id = intval(strtotime($row[$key]));                            
              // если время одинкаовое? инкрементируем ключ, пока не будет разный
              if (isset($this->items[$id])) { 
                  while (isset($this->items[$id])) { $id++; }  
              }
            }
            */
            // items[$id] = $tmp;   
            $this->append($tmp, $id);
        }

        $this->db->free_result($res);
        $this->load_after();

        return $this;
    }

    function load_after() {
    }

    /**
     * @param abs_collection_item $item
     * @param bool $id
     * @return $this
     */
    function append(abs_collection_item $item, $id = false) {

        // if external
        $item->set_container($this);

        if (false === $id)
            $this->items[] = $item;
        else
            $this->items[$id] = $item;

        return $this;
    }

    /**
     * @param abs_collection_item $item
     * @return $this
     */
    function prepend(abs_collection_item $item) {
        array_unshift($this->items, $item);
        return $this;
    }

    /**
     * Warn! Import external items
     * @param array(IAbs_Collection_Item) $items
     */
    function set_items($items) {
        $this->items = $items;
        foreach ($this->items as $i) {
            $i->set_container($this);
        }
    }

    /**
     * @todo make clean working fields
     * user reg fail?
     */
    function filter_input_data(&$data) {
        /*
        foreach ($data as $k => $v) {
            if (!$this->in_working_set($k) && $k != $this->get_key()) unset($data[$k]);
        }
        */
    }


    /**
     * Create new item
     * This is alias for @see self::modify()
     * @return integer new itemID
     */
    function create($data) {
        return $this->modify($data);
    }

    /**
     * Change item
     *
     * @param array data  (mostly post)
     * @param mixed ID if update, false if new
     * @return integer new item id
     */
    function modify($data, $id = false) {

        $this->filter_input_data($data);

        $this->_last_id = false;

        if (false === $this->modify_before($data)) return false;
        if (!$id && false === $this->create_before($data)) return false;


        //
        // Insert
        //
        if (empty($id)) {

            if ($this->config->get('debug')) {
                core::dprint_r(array('NEW-ITEM:', $data));
            }

            // create new item
            $elm = $this->_alloc($data);

            // dyn append to list
            $this->append($elm, $elm->id);

            // dyn append to list
            $this->_last_id  = $this->is_key_autoincrement() ? $elm->get_id() : count($this->items);
        }
        //
        // Update
        //
        else {

            $elm = $this->get_item_by_id($id);
            if ($elm === false) {
                throw new collection_exception("Modify unloaded/removed item #{$id}");
            }

            $elm->modify($data);
            $this->_last_id = $elm->id;
        }

        if (!$id) $this->create_after($data, $this->_last_id);
        $this->modify_after($data, $this->_last_id);

        return $this->_last_id;
    }

    function modify_before(&$data) {
    }

    function modify_after($data, $last_id) {
    }

    function create_before(&$data) {
    }

    function create_after($data, $last_id) {
    }

    /**
     * Gets item last modified @see last_id
     * @return abs_collection_item
     */
    function get_last_item() {
        if (!$this->_last_id) return null;

        return $this->get_item_by_id($this->_last_id);
    }

    /**
     * Allocate item for modification
     * @return abs_collection_item
     */
    function alloc($data = array()) {

        // generate empty entity
        foreach ($this->get_vfs() as $k => $v) {
            if (!isset($data[$k])) $data[$k] = '';
        }

        if (empty($data['id'])) $data['id'] = 0;

        $item = $this->_alloc($data, true, array('allocated' => true));

        return $item;
    }

    /**
     * Intrenal: create item instance (new item_class)
     *
     * @param mixed $data
     * @param mixed $verified
     */
    protected function _alloc($data, $verified = null, $_config = array()) {
        $class = $this->item_class;

        $config = clone $this->config;

        if (!empty($_config)) {
            $config->merge($_config);
        }

        $this->alloc_before($class, $config, $data, $verified);

        $item = new $class(
            $this
            , $config
            , $data
            , $verified
        );
        $this->alloc_after($item, $class, $config, $verified);

        return $item;
    }

    protected function alloc_before(&$class, &$config, &$data, &$verified) {
        ;
    }

    protected function alloc_after($item, $class, $config, $verified) {
        ;
    }

    /**
     * Return empty item,
     * used for unexisted data, stub etc
     * @return CollectionItem
     */
    function get_empty_item() {
        return $this->alloc();
    }

    /**
     * Set working fields for collection (inherit on items create)
     * Without params it clear weorking fields
     * Warn! use this only with UPDATE item or validate
     * @param mixed varargs OR array
     * @return abs_collection
     */
    public function set_working_fields() {
        $this->working_fields = array();
        $count                    = func_num_args();

        if (!empty($count)) {

            $args = func_get_args();

            // simple array
            if ($count == 1 && is_array($args[0])) {
                $this->working_fields = $args[0];
            } else {
                // varargs
                foreach ($args as $item) {
                    $this->working_fields [] = $item;
                }
            }
        }

        // update childs
        if ($this->count())
            foreach ($this as $v) {
                $v->set_working_fields($this->working_fields);
            }

        return $this;
    }

    /** called on create from items */
    function get_working_fields() {
        return $this->working_fields;
    }

    /**
     * Check field available
     */
    protected function in_working_set($k) {
        return (empty($this->working_fields) || in_array($k, $this->working_fields));
    }

    /**
     * drop
     * @param null|id if null, remove_all used
     * @return self
     */
    function remove($id) {

        if (!$id) {
            core::dprint(array('Empty id in %s remove()', $this->get_name()));

            return $this;
        }

        $elm = $this->get_item_by_id($id);
        if ($elm === false) throw new collection_exception('Try to remove unexisted data #' . $id);
        $elm->remove();
        unset($this->items[$elm->id]);

        return $this;
    }

    /**
     * drop all
     * @return self
     */
    function remove_all() {
        if (!empty($this->items))
            foreach ($this->items as $k => $v) {
                $this->remove($v->get_id());
            }

        return $this;
    }

    /**
     * drop all indirect
     * (without creating items and notify them)
     * @return self
     */
    function remove_all_fast() {
        $w   = $this->get_where();
        $sql = "DELETE FROM " . $this->get_table() . ($w ? " WHERE {$w}" : '') . ';';
        $this->db->query($sql);
        $this->clear();

        return $this;
    }

    /**
     * Set database table
     */
    function set_table($t) {
        if (!empty($t)) {
            $t = $this->get_prefix() . $t;
            $t = str_replace('%class%', $this->get_name(), $t);
        } else {
            $this->readonly = true;
            $t              = false;
        }
        $this->config->set('table', $t);
    }

    /**
     * Get sql table with prefix
     * @return string sql_table
     */
    function get_table() {
        return $this->config->table;
    }

    /** sql table prefix */
    function get_prefix() {
        return $this->config->get('prefix');
    }

    function get_name($short = false) {
        // @todo short name
        return $this->_name;
    }

    /**
     * @desc
     */
    function get_max_position() {
        // @todo
    }

    function move_position($from, $to, $factor) {
    }

    /**
     * Flip items positions
     * Integrate with jquery item positioning
     * @param integer src_id
     * @param integer dst_id
     * @param array ids[] = id
     * @param array pos[] = new_pos
     */
    function flip_positions($src, $dst, $ids, $positions) {

        // @todo check for range
        // make new positions

        if (!empty($positions))
            foreach ($positions as $key => $pos) {
                $test = $this->update_item_fields($ids[$key], 'position = ' . $pos);
            }
    }

    /**
     * Check field unique
     * Makes query to db for each check
     * @param string field vf_name
     * @param mixed value
     * @param mixed skip this id
     * @return bool
     */
    function check_unique_value($name, $value, $id = false) {

        $value = trim($value);

        if (empty($value)) return false;

        $vf      = $this->get_field($name);
        $is_text = ($vf['type'] == 'text');

        if ($is_text) $value = strings::strtoupper($value);

        $value = $this->format_field_sql($name, $value);

        $where_ = $id ? ("id <> " . (int)$id . " AND ") : '';

        $sql = "SELECT id FROM " . $this->get_table() . " WHERE " . $where_ . ($is_text ? "UCASE({$name})" : $name) . " " . ($is_text ? '=' : '=') . " {$value}";

        return (0 == $this->db->sql_numrows($this->db->query($sql)));
    }

    /**
     * подготовка к редактированию
     * Item must be loaded
     */
    function prepare2edt($id = null) {

        $this->prepare2edt_before($id);
        $elm = $id ? $this->get_item_by_id($id) : $this->alloc();
        core::dprint('prepare2edt id=' . $id);
        $elm->prepare2edt();
        return $elm;

    }

    function prepare2edt_before($id) {
    }

    /**
     * Items as array
     * @param string use only one key in result
     * @return array items as array
     */
    function as_array($key = false) {
        if (empty($this->items)) return false;
        $out = array();
        foreach ($this->items as $id => $v) {
            $out[$id] = $v->as_array($key);
        }

        return $out;
    }

    /**
     * Convert to json
     * @return string json
     */
    function as_json($fields = false, $assoc = false) {

        $out = array();

        if (!empty($this->items)) {
            foreach ($this->items as $id => $v) {
                $row = $v->as_json($fields);
                if ($assoc) {
                    $out[$id] = $row;
                } else {
                    $out []= $row;
                }
            }
        }

        return functions::json_encode($out);
    }

    /**
     * Set tpl namespace
     * (auto adds prefix 'tpl_')
     * @return self
     */
    function set_tpl_table($name) {
        $this->config->tpl_table = $name;

        return $this;
    }

    /**
     * Get tpl table
     */
    function get_tpl_table() {
        return $this->tpl_prefix . $this->config->get('tpl_table');
    }

    protected $_render_key = 'id';

    /**
     * Set render enable|key via @see is_render_by_key method
     *
     * @param mixed $k
     * @return abs_collection
     */
    function set_render_key($k = null) {
        $this->_render_key = empty($k) ? 'id' : $k;

        return $this;
    }

    function render_with_meta($fields = null) {
        return $this->render2edt(true, $fields);
    }

    /**
     * @param bool true, не добавлять в шаблон
     * @return {data: rendered, model: vfs}
     */
    function render2edt($notpl = false, $fields = false) {

        $tpl_table = $this->get_tpl_table();

        $out = array();

        core::dprint(array('render2edt  : %s x %d [%s]', $tpl_table, $this->count(), ($this->is_render_by_key() ? '+' : '-')));

        if (!$notpl) $this->get_tpl_parser()->clear_assign($tpl_table);

        if (!empty($this->items)) {

            /** @var abs_collection_item $v */
            foreach ($this as $k => $v) {
                // prevent  $out[]
                if ($this->is_render_by_key())
                    $out[$v->get_data($this->_render_key)] = $v->render2edt($fields);
                else
                    $out[] = $v->render2edt($fields);
            }
        }

        // by id hack
        // if ($this->is_render_by_key()) {
        // }

        $rendered = array(
            'ctype'  => $this->ctype ? $this->ctype->render() : array(),
            'data'   => $out,
            'fields' => $this->fields()
        );

        if (!$notpl) {
            $this->get_tpl_parser()->assign($tpl_table, $rendered);
        }

        return $rendered;
    }

    /**
     * @return Smarty3
     * @deprecated do not use parser directly
     */
    function get_tpl_parser() {
        return core::lib('tpl_parser');
    }

    /**
     * get items
     */
    function get_items() {
        return $this->items;
    }

    /**
     * есть ли элементы
     */
    function is_empty() {
        return empty($this->items);
    }

    /**
     * очистить
     *
     * clear all:
     *  clears position
     */
    function clear($all = false) {

        $this->_ids_cache = array();
        $this->items      = array();

        $this->config->clear('no_preload');
        $this->config->clear('load_only_id');

        if ($all) {
            $this->set_working_fields();
            $this->config->clear('order_sql');
            $this->config->clear('where_sql');
            $this->config->clear('limit_sql');
            $this->config->clear('join_where_sql');
            $this->config->clear('join_sql');
            // restore initial order
            $this->set_order($this->_order_sql);
        }

        return $this;
    }

    /**
     * @return self
     */
    function set_load_only_id($id) {
        return
            $this->config->set('load_only_id', $id)
                ->set_limit(1);
    }

    /**
     * Load only one item
     * @return abs_collection_item
     */
    function load_only_id($id) {

        if (empty($id)) throw new collection_exception('load_only_id empty ID');

        $this->clear()->config->load_only_id = $id;

        return $this->load()->get_item();

    }

    /**
     * посчитать количество элементов
     */
    function count() {
        return @intval(count($this->items));
    }

    /**
     * посчитать количество через sql
     * (с параметрами Load())
     */
    function count_sql() {
        $sql = $this->build_sql(true);
        $res = $this->db->query($sql);
        $row = $this->db->fetch_row($res);
        $this->db->free_result($res);

        return @intval($row['s_count']);
    }

    /**
     * Simple cache ids for items
     * 'id' => index
     * Call by @see self::get_item_by_id
     * Clear in @see self::clear
     * @return integer null = fail
     */
    private function _cache_id($id, $set = false) {
        if (!$set) return isset($this->_ids_cache[$id]) ? $this->_ids_cache[$id] : null;
        $this->_ids_cache[$id] = $set;
    }

    function __invoke($id) {
        return $this->get_item_by_id($id);
    }

    function merge($collection) {
        if ($collection->count()) {
            $this->items = array_merge($this->items, $collection->get_items());
        }

        return $this;
    }

    /**
     * элемент по Id
     * @return abs_collection_item or false
     */
    function get_item_by_id($id) {
        return $this->get_item_by_prop($this->get_key(), $id);
    }

    /**
     * элемент по Name
     * @return abs_collection_item or false
     */
    function get_item_by_name($id) {
        return $this->get_item_by_prop('name', $id);
    }

    /**
     * Items by props collection
     */
    private function get_item_by_prop_a($props) {
        foreach ($this as $item) {
            $res = true;
            foreach ($props as $k => $v) if ($item->get_data($k) != $v) {
                $res = false;
                break;
            }
            if ($res) return $item;
        }

        return null;
    }

    /**
     * поиск элемента по значению свойства
     * @param string property name
     * @param mixed val
     * @param bool no_case |casesensitive, default true
     * @return abs_collection_item or false
     */
    function get_item_by_prop($name, $val = null, $nc = false) {

        $tmp = false;

        // no items?
        if (empty($this->items)) return $tmp;

        if (is_array($name)) {
            return $this->get_item_by_prop_a($name);
        }

        if ($nc) $val = strings::strtolower($val);

        // get cache
        if ('id' == $name && (null !== ($k = $this->_cache_id($val))) && isset($this->items[$k])) {
            return $this->items[$k];
        }

        foreach ($this->items as $k => $v) {
            if ($v->isset_data($name)) {
                $_val = $v->get_data($name);
                $_val = ($nc) ? strings::strtolower($_val) : $_val;
                if ($_val == $val) {
                    $tmp = $this->items[$k];
                    break;
                }
            }
        }

        // set cache
        if ('id' == $name) {
            $this->_cache_id($val, $k);
        }

        return $tmp;
    }

    /**
     * Run method on all children
     *
     * $users->invoke('dump');
     * $users->invoke(function($item){$item->dump();});
     *
     * @param mixed $method
     * @param mixed $params
     * @return abs_collection self
     */
    function invoke($method, $params = null) {

        if (!$this->count()) return;

        foreach ($this as $item) {

            if ($item->get_data($method) instanceof Closure) {
                $method = $item->get_data($method);
            }

            if (is_callable($method)) {
                $method($item);
            } else {
                call_user_func(array($item, $method), $params);
            }
        }

        return $this;
    }

    /**
     * debug dump
     */
    function dump() {

        core::dprint('[DUMPING] ' . get_class($this), core::E_INFO);
        foreach ($this->items as $k => $v) {
            $v->dump();
        }
    }

    /**
     * set cfg to all childs
     * @depricated
     * @return self
     */
    /*
    function config->set_ex($var, $value) {
        $this->config->set($var, $value);
        if (!empty($this->items))
            foreach ($this->items as &$v) {
                $v->config->set($var, $value);
            }

        return $this;
    }
    */

    /**
     * Render to front
     * @param array fieilds filter
     */
    function render($fields = false) {

        if (empty($this->items)) return false;

        $out = array();
        foreach ($this->items as $k => $v) {
            // prevent  $out[]
            if ($this->is_render_by_key())
                $out[$v->get_data($this->_render_key)] = $v->render($fields); // silent
            else
                $out[] = $v->render($fields); // force append
        }

        return $out;
    }

    /**
     * @return self
     */
    function is_render_by_key($fl = null) {
        if ($fl === true) {
            $this->config->set('render_by_key', true);

            return $this;
        }
        if ($fl === false) {
            $this->config->set('render_by_key', false);

            return $this;
        }
        if ($fl !== null) {
            $this->config->set('render_by_key', true);
            $this->set_render_key($fl);

            return $this;
        }

        return $this->config->get('render_by_key');
    }

    /**
     * Загрузка вспомогательных элементов
     * (дочерние объекты, зависимые объекты...)
     * Выполняет соответствующий код для всех элементов коллекции
     */
    function load_secondary($options = null) {
        if ($this->is_empty()) return $this;
        foreach ($this->items as $item) $item->load_secondary($options);

        return $this;
    }

    function render_secondary() {
        if ($this->is_empty()) return $this;
        foreach ($this->items as $item) $item->render_secondary();

        return $this;
    }

    /**
     * iterator
     */
    private $_loop_index = 0;

    /**
     * rewind
     * @return self
     */
    public function rewind() {
        $this->_loop_index = 0;

        return $this;
    }

    /**
     * next
     */
    public function next($force_rewind = false) {

        if ($force_rewind) $this->rewind();
        if (empty($this->items) || $this->_loop_index >= count($this->items)) return false;

        $keys = array_keys($this->items);
        $key  = $keys[$this->_loop_index];

        if (isset($this->items[$key])) {
            $this->_loop_index++;

            return $this->items[$key];
        } else {
            // auto rewind on exit
            $this->rewind();
        }

        return false;
    }

    /**
     * Get item. SEE parm desc!!
     * @param mixed
     *
     *   false - get first
     *   true  - get next
     *   index - get index
     *
     * @return abs_collection_item
     */
    public function get_item($index = false, $clone = false) {
        $item = false;
        if ($index === false) {
            $item = $this->next(true);
        } else if ($index === true) {
            $item = $this->next(false);
        } else {
            if (isset($this->items[$index]))
                $item = $this->items[$index];
        }

        if ($clone) return (clone $item);

        return $item;
    }


    /**
     * Multiple loaded items
     * only for test
     * @return $this
     */
    function _fake_items($long = 20) {

        if (!$this->count()) {
            core::dprint('_fake_items empty', core::E_ERROR);

            return;
        }

        $count = count($this->items) - 1;

        $items = array_values($this->items);
        for ($i = 0; $i < $long; $i++) {
            $this->append(
                clone $items[$count ? rand(0, $count) : $count]
            );
        }

        return $this;
    }

    /**
     * Update item fields
     * Service method, call with caution
     * Data must be validated!
     * @param array|integer item id
     * @param array|string sql array('key=>'value',...)
     * @return bool result
     */
    public function update_item_fields($id, $data) {

        $sql_in = array();

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $value    = $this->format_field_sql($key, $value);
                $sql_in[] = "{$key} = {$value}";
            }
            $data = implode(',', $sql_in);
        }

        $key = $this->get_key();

        if (is_array($id)) {
            foreach ($id as &$_id) $_id = $this->format_field_sql($key, $_id);
        } else $id = $this->format_field_sql($key, $id);

        $where_id = is_array($id)
            ? ("IN (" . implode(',', $id) . ")")
            : "= {$id}";

        $this->_last_query = $sql = "UPDATE LOW_PRIORITY {$this->config->table} SET {$data} WHERE {$key} " . $where_id . ';';
        $res               = $this->db->query($sql);

        return $res;
    }

    /**
     * Update item fields
     * Operate with loaded data ids
     * @param integer item id
     * @param array|string sql
     * @return bool result
     */
    public function update_same_fields($data) {

        if ($this->is_empty()) return;

        $ids = array();
        foreach ($this->items as $i) {
            $ids [] = $i->id;
        }

        $this->update_item_fields($ids, $data);

    }

    /**
     * Create temporary item, validate data
     * @return array valiadtion result
     */
    function is_valid($data) {

        $item = $this->alloc($data);

        $item->is_valid();

        return $item->get_validation_result();
    }

    /**
     * Format stuff
     * ------------
     */

    /**
     * Convert value to SQL form on
     * Prepare item's data to database output
     * @param mixed field key
     * @param mixed field value
     *
     * @return mixed formatted value
     */
    function format_field_sql($key, $fld, $vf = null) {

        if (!isset($vf)) {
            $vf = $this->field($key);
        }

        $type = $vf['type'];

        switch ($type) {

            case 'timestamp':
                $fld = "to_timestamp('$fld', 'DD.MM.YYYY HH24:MI')"; // postgres timestamp      
                break;

            case 'numeric':
                $fld = floatval($fld);
                if (!empty($vf['long'])) $fld = sprintf('%u', $fld);
                break;

            case 'unixtime':
            case 'position':
                $fld = intval($fld);
                break;

            case 'boolean':
                // sqlite doesnt supp true/false
                $fld = $fld ? '1' : '0';
                break;

            case 'image':
            case 'file':
                if (is_array($fld)) {
                    // remove unused stuff from asving to storage
                    if (isset($fld['thumbnail'])) unset($fld['thumbnail']);
                }
                $fld = empty($fld) ? "''" : "'" . serialize($fld) . "'";
                break;

            default:
                $fld = "'" . $this->db->escape($fld) . "'";
                break;
        }

        return $fld;
    }

    /**
     * format on load
     * Cast data to original type
     */
    function format_field_on_load($vf, &$fld) {

        $type = $vf['type'];
        switch ($type) {
            case 'unixtime':
            //---------------
                $fld = (int)$fld;
                break;

            case 'boolean':
            //---------------
                $fld = (bool)$fld;
                break;

            case 'numeric':
            //---------------
                if (!empty($vf['float'])) $fld = (float)$fld;
                else
                    if (!empty($vf['long'])) $fld = (float)$fld; /*long*/
                    else $fld = (int)$fld;

                break;

            case 'file':
            case 'image':
            //---------------

                // if length of sql-text is not enought, unserialize will fail!

                // unserialize value from storage

                if (!empty($fld) && !is_array($fld)) {
                    $fld = @unserialize($fld);
                    if (false === $fld) {
                        core::dprint(array('WARNING! Unserialize failed %s : %s'
                            , get_class($this), $vf['type'])
                            , core::E_ERROR);
                    }
                }

                if (!is_array($fld)) $fld = array();

                if (!empty($fld['file'])) {

                    $spacing_path = '';
                    if (!empty($vf['spacing'])) {
                        $spacing_path = ('/' . substr(substr($fld['file'], strrpos($fld['file'], '/', 0)), 1, $vf['spacing']));
                    }

                    // make url for it
                    $fld['url'] = //core::get_instance()->get_router()->make_url(
                        core::get_instance()->get_static_url() . '/uploads/' . $vf['storage']
                        . $spacing_path
                        . substr($fld['file'], strrpos($fld['file'], '/', 0));

                    if (!empty($vf['thumbnail'])) {
                        $fld['thumbnail'] = array(
                            'url' => preg_replace('@\.([^\.]+)$@', '.thumb.$1', $fld['url'])
                        , 'file'  => preg_replace('@\.([^\.]+)$@', '.thumb.$1', $fld['file'])
                        );
                    }

                    if (!empty($vf['original'])) {
                        $fld['original'] = array(
                            'url' => preg_replace('@\.([^\.]+)$@', '.orig.$1', $fld['url'])
                        , 'file'  => preg_replace('@\.([^\.]+)$@', '.orig.$1', $fld['file'])
                        );
                    }
                }
                break;
        }
    }

    /**
     * format on view
     */
    function format_field_on_view($vf, &$fld, $item) {
        $type = $vf['type'];

        switch ($type) {

            case 'text':
            //---------------

                // escape with format
                if (empty($vf['no_format'])) {
                    $fld = htmlspecialchars($fld, ENT_QUOTES, 'UTF-8');
                }

                break;

            case 'unixtime':
            //---------------

                if ($fld) {
                    $format = isset($vf['format']) ? $vf['format'] : 'd.m.Y H:i';
                    $fld    = $fld ? date($format, intval($fld)) : '';
                } else {
                    $fld = '';
                }
                break;

            case 'numeric':
            //---------------

                if (isset($vf['currency']) && $vf['currency']) { // просмотр    
                    if (is_array($vf['currency']))
                        $fld = number_format($fld, $vf['currency'][0], $vf['currency'][1], $vf['currency'][2]);
                    else
                        $fld = number_format($fld, 2, ',', '.');
                }

                if ($format = @$vf['format']) {
                    $fld = sprintf("{$format}", $fld);
                }

                break;

            case 'virtual':
            //---------------

                if (!empty($vf['method'])) {
                    $method = $vf['method'];
                    $fld    = $item->render_virtual($method, 'view');
                }
                break;
        }
    }

    /**
     * format on edit
     */
    function format_field_on_edit($vf, &$fld, $item) {
        $type = $vf['type'];
        switch ($type) {

            case 'text':
            //---------------

                // array passed (?serialized)
                if (is_array($fld)) return;

                $fld = htmlspecialchars($fld);
                break;

            case 'timestamp':
            //---------------

                // skip empty dates
                $format = isset($vf['format']) ? $vf['format'] : 'd.m.Y H:i';
                if (!empty($fld))
                    $fld = create_date(strtotime($fld), $format);
                else $fld = '';
                break;

            case 'unixtime':
            //---------------

                $format = isset($vf['format']) ? $vf['format'] : 'd.m.Y H:i';

                if (empty($fld) && @$vf['default'] == 'now') {
                    $fld = time();
                }

                $fld    = $fld ? date($format, intval($fld)) : '';
                break;

            case 'list':
            //---------------

                if ($full_props !== false && isset($full_props['values'][$fld]) && (!isset($full_props['no_format']))) {
                    $fld = $full_props['values'][$fld];
                }
                break;


            case 'boolean':
            //---------------

                $t_ = intval($fld); // 1 OR 0
                break;

            case 'numeric':
            //---------------

                $fld = floatval($fld);

                if ($format = @$vf['format']) {
                    $fld = sprintf("{$format}", $fld);
                }

                if (isset($vf['currency']) && $vf['currency']) { // просмотр    
                    if (is_array($vf['currency']))
                        $fld = number_format($fld, $vf['currency'][0], $vf['currency'][1], $vf['currency'][2]);
                    else
                        $fld = number_format($fld, 2, ',', '.');
                }

                break;

            case 'virtual':
            //---------------

                if (!empty($vf['method'])) {
                    $method = $vf['method'];
                    // call virtual_method to retrive data
                    $fld = $item->render_virtual($method, 'edit');
                }
                break;
        }
    }

    /**
     * format on delete
     */
    function format_field_on_remove($vf, &$fld, $current) {

        $type = $vf['type'];
        switch ($type) {
            case 'image':
            case 'file':
                if (!empty($current['file'])) {
                    fs::unlink($current['file']);
                    if (!empty($current['thumbnail']['file']))
                        fs::unlink($current['thumbnail']['file']);

                    if (!empty($current['original']['file']))
                        fs::unlink($current['original']['file']);
                }
                break;
        }
    }

    /**
     * format on modify
     */
    function format_field_on_modify($vf, &$fld, $current) {
        $type = $vf['type'];
        /**
         * @todo must validate and normalize all input (utf8)
         */

        switch ($type) {

            case 'boolean':
            //---------------

                $fld = intval($fld) ? 1 : 0; // 1 OR 0
                break;


            case 'text':
            //---------------

                // normalize utf!

                // remove trailing <br> (jquery wys)
                // if (preg_match('/<br>$/', $fld)) $fld = preg_replace('/<br>$/', '', $fld);
                // validate here

                if (!empty($vf['no_format'])) break;

                if (!empty($vf['format'])) {
                    $fld = core::lib('validator')->parse_str($fld, $vf['format']);
                } else {
                    // default is strip tags
                    // use empty format for deny this rule
                    // var_dump($fld, strip_tags($fld, false));
                    if (!isset($vf['format'])) $fld = core::lib('validator')->parse_str($fld, 'strip_tags');
                }

                break;


            case 'position':
            //---------------

                $fld = (int)$fld;
                break;


            case 'numeric':
            //---------------

                if (is_string($fld)) {
                    // convert
                    $fld = preg_replace('#^[^\d\.,]$#', '', $fld);
                    $fld = str_replace(',', '.', $fld);
                }
                $fld = floatval($fld);
                break;

            /**
             * Время unix приходит в строковом формате,
             * изменяем его в числовой вид
             * If time already number, pass it throw.
             */

            case 'unixtime':
            //---------------

                // raw, wihtout format
                if (isset($vf['no_check'])) break;

                // default date modificator

                // @todo fix @see abs_collection_item::format_fields()
                if ($fld == 'now' /*isset($vf['defailt']) && $vf['defailt'] == 'now'*/) $fld = time();

                // core::var_dump($fld);

                if (empty($fld)) $fld = 0;
                else
                    if (ctype_digit($fld) && intval($fld) > 9999) $fld = intval($fld); // this is raw unix
                    else $fld = strtotime($fld);

                break;


            case 'file':
            case 'image':
            //---------------

                // $control = $this->create_control('image');
                // $control->modify($vf, $fld);

                if (!empty($fld)) {
                    $fld = control_image::process_modify($this, $vf, $fld, $current);
                } else {
                    $fld = $current; // original
                }

                break;

            default:
                break;
        }
    }

    /**
     * форматировать поле для вывода
     * 'id' => 'value'
     *
     * @todo make CONTROLS Object
     *
     * @param string key
     * @param mixed value
     * @param array options
     * использовать дополнительные описания полей
     * (например depend для формирования зависимостей при выводе)
     * 'edit'      - prepare data to edit
     * 'view'      - on view (frontend)
     * 'modify'    - on data submit             (default)
     * 'load'      - on database load
     *
     * @param mixed current field value
     */

    public function format_field($key, $fld, $opt, $current = false, $item = false) {

        $vf = $this->get_field($key);

        if ('load' == $opt) $this->format_field_on_load($vf, $fld); else
            if ('edit' == $opt) $this->format_field_on_edit($vf, $fld, $item); else
                if ('view' == $opt) $this->format_field_on_view($vf, $fld, $item); else
                    if ('remove' == $opt) $this->format_field_on_remove($vf, $fld, $current); else
                        if ('modify' == $opt) $this->format_field_on_modify($vf, $fld, $current);
                        else
                            throw new collection_exception('Undefined or empty format type ' . $opt);

        return $fld;
    }


    /**
     * @return abs_control
     */
    protected function create_control($type) {
        $class = 'control_' . $type;
        if (!class_exists($class, 0)) fs::req('modules/core/abstract/controls/' . $type . '.php');

        return new $class;
    }

    /**
     * compare 2 collections
     * @return bool
     */
    public function compare(abs_collection $subj) {
        $subj->rewind();
        while ($item = $subj->next()) {
            if (!(($tmp = $this->get_element_by_id($item->id)) && $tmp->get_data() == $item->get_data())) return false;
        }
        if ($subj->count() != $this->count()) return false;

        // passed
        return true;
    }

    /**
     * Check w/positions
     * @param null|bool
     */
    public function with_positions($fl = -1) {
        if ($fl === -1) return $this->_with_positions;
        $this->_with_positions = (bool)$fl;
    }

    /**
     * Is extra fields enabled?
     * @return string extra fields storage
     */
    public function with_extra_fields($fl = null) {
        if ($fl !== null) $this->_with_extra_fields = (bool)$fl;

        return $this->_with_extra_fields;
    }

    /**
     * Check w/dependencies
     *
     * @param array|boolean
     *  true === load all
     *  [dep1, dep2] = load specified
     *
     * @return self:bool Return $this if set flag called (for chaining)
     */
    public function with_deps($fl = null) {
        if (!isset($fl)) return $this->_with_deps;
        $this->_with_deps = $fl;

        return $this;
    }

    /**
     * Disable fields
     */
    public function disable_extra_fields() {
        $this->_with_extra_fields = false;
    }

    /**
     * @return self
     */
    function with_renderer_cache($fl) {
        if (!empty($this->items))
            foreach ($this->items as $v)
                $v->with_render_cache($fl);

        return $this;
    }


    /**
     * Load extra fields for this collection
     * depends on core class 'extra_fields'
     * must be loaded
     *
     * @throws collection_exception
     */
    public function load_extra_fields() {

        if (empty($this->DOMAIN)) {
            throw new collection_exception('With extrafields option used without self::DOMAIN in ' . get_class($this));
        }

        if (!core::get_instance()->class_registered('extra_fields')) {
            throw new collection_exception('Extra fields not loaded');
        }

        // filter for domain
        $this->extra_fields = core::get_instance()->class_register('extra_fields')->get_by_domain($this->DOMAIN);
        $this->extra_fields->update_collection($this->fields);
    }

    /**
     * Assign extra fields for item
     * Called to blame static in collection constructor
     * @param object user
     */
    public function load_extra_fields_data(IAbs_Collection_Item $item) {

        $ef = $this->with_extra_fields();
        if ($ef) {
            // $ef[0] - module
            // $ef[1] - class
            $efdata = core::module($ef[0])->class_register($ef[1], array('no_preload' => true), true);
            $efdata->set_where('pid = %d', $item->id);
            $efdata->load();
            $efdata->set_parent_extra($this->extra_fields);

            // inject extra data to object
            if (!$efdata->is_empty()) {
                $efdata->rewind();
                while ($efitem = $efdata->next()) {
                    $key = $this->extra_fields->get_item_by_id($efitem->fid)->key;
                    $item->set_data($key, $efitem->value);
                }
            }

            return $efdata;
        }

        return false;

        // append valid fields
    }

# SPECIALS ==>

    /**
     *     Добавить слайс (срез по дате)
     *     по дате MONTH_FROM, YEAR, MONTH_TO
     */

    function create_slice($m, $y, $m1 = false) {

        $m  = intval($m);
        $y  = intval($y);
        $m1 = ($m1 === false) ? false : intval($m1);

        if (empty($this->items)) return false;
        foreach ($this->items as $k => $v) {

            $stime = strtotime($v->data['date']);
            $m_    = intval(date('m', $stime));
            $y_    = intval(date('Y', $stime));

            // core_c::cprint("[color=red]{$m},{$y} : {$m_},{$y_}[/color]");
            if ($m1 === false) {
                // not match!
                if ($m != $m_ || $y != $y_) {
                    unset($this->items[$k]);
                }
            } else {
                if ($m_ < $m || $m_ > $m1 || $y != $y_) {
                    unset($this->items[$k]);
                }
            }
        }

        return count($this->items);
    }

    /**
     *   create_slice_ex
     *   Создать срез
     *   ['to_month'] ['from_month'] ['from_year']
     *   и по остальным параметрам
     *
     * @param array параметры сортировки [key]=[value(mixed)]
     */

    function create_slice_ex($data_) {

        if ($this->is_empty() || empty($data_) || !is_array($data_))
            return false;

        if (isset($data_['from_month']))
            $this->create_slice(
                $data_['from_month'], $data_['from_year'], $data_['to_month']
            );

        $unset_ = array('from_month', 'from_year', 'to_month', 'c', 'ref_submit', '2print', 'simple');

        // cropppingg
        foreach ($unset_ as $k => $v)
            if (isset($data_[$v]))
                unset($data_[$v]);

        /*    фильтруем по оставшимся даных
        */

        foreach ($data_ as $k => $v) {

            core::cprint('[color=green]filter=[/color]' . $k . ' : ' . $v);

            if ($v != -1 || (is_array($v) && !empty($v))) { /* -1 - все */
                foreach ($this->items as $k_item => $v_item) {
                    if ((is_array($v) && !in_array($v_item->get_data($k), $v))
                        || (!is_array($v) && $v_item->get_data($k) != $v)
                    ) unset($this->items[$k_item]); //kill kill kill

                }
            }
        }

        return count($this->items);

    }

    /** @return int ctype_id */
    function get_ctype_id() {
        return $this->_ctype_id;
    }

    /**
     * Get CTYPE
     * @return ctype_item
     */
    function get_ctype() {
        return $this->ctype;
    }

    /**
     * Get ctype name
     * extract constant CTYPE to ctype_id
     */

    function _get_ctype() {

        if (!isset($this->_ctype)) {

            if (loader::is_php53()) {
                $this->_ctype = defined('static::CTYPE') ? static::CTYPE : false;
            } else {
                // php 5.2, no lsb
                $class        = get_class($this);
                $reflector    = new ReflectionClass($class);
                $this->_ctype = $reflector->hasConstant('CTYPE') ? $reflector->getConstant('CTYPE') : false;
            }
        }

        return $this->_ctype;
    }

    function get_root() {
        $this->_root;
    }

    /**
     * Gets generator
     * @param mixed $classes
     * @return collection_generator
     */
    static function get_generator($classes = false) {
        require_once "modules/core/abstract/collection/generator.php";

        return new collection_generator($classes);
    }

    /** @return null_collection */
    static function get_null_collection() {
        return new null_collection();
    }

}


/**
 * NUll collection
 */
class null_collection extends abs_collection {

    protected $fields = array('id' => array('type' => 'numeric'));

    function __construct() {
        $this->db   = db_loader::get(array('engine' => 'null'));
        $this->core = core::get_instance();
        $this->prepare_fields();
    }
}

/*

some diagrams:

Item modification flow (call modify())
              |
     +--------------------+
     | collection::modify |
     +--------+-----------+
              id
          +----+----+
      new |         | update (must be loaded)
          |         |
      +---+         +---------+
      |                       |
new item(cfg, data)         item::modify(data)
      |                       |
item::load(data)              |
      |                       |
      +----------+------------+
                 |
           item::save()
                 |
      item::modify_after(data)
                 |
           return itemID
*/
  
