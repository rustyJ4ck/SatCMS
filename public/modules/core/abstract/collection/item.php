<?php

/**
 * Colection item
 * @see collection for more info
 *
 * Warn!
 * Dont use private variables in derived classes - use protected instead
 * __set, __get methods keep them in common data.
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */


/**
 * Item interface
 */
interface model_item_interface {
    function get_url(); // break php5.4 strict
}

/**
 * Class model_behavior
 */
class model_behavior {

    /**
     * @var model_item parent
     */
    protected $model;

    function set_model(model_item $model) {
        $this->model = $model;
        $this->configure();
    }

    /** @abstract init */
    function configure() {}
}

/**
 * Class model_behaviors
 */
class model_behaviors extends aregistry {
}

/**
 * Collection element
 */
class model_item extends abs_data implements model_item_interface {

    /**
     * @var ModelBehaviors
     */
    protected $behaviors;

    /**
     * Controls
     */
    protected $controls;

    /**
     * @var model_collection
     */
    protected $container;

    /**
     * @var collection_params
     */
    public $config;

    /**
     * Is new item flag
     */
    protected $_is_new = true;

    /**
     * Allocated item flag
     */
    protected $_is_allocated = false;

    /**
     * w/renderer cache
     */
    protected $_with_render_cache = true;

    /**
     * Modify items changes only this fields
     * @see self::set_working_fields()
     */
    protected $working_fields;

    /**
     * Formatted fields cache
     */
    private $render_cache = array();

    /** @var dbal */
    protected $db;

    protected $_key = 'id';

    protected $is_dummy = false;

    /** Delegates methods to collection */
    private static $_delegate_methods = array(
        '_get_ctype',
        'get_ctype',
        'get_ctype_id',
        'with_deps',
        'get_class',
        'get_field',
        'fields',
        'field',
        'get_fields_keys',
        'has_field',
        'is_key_autoincrement'
    );

    /**
     * Create an item
     * new item: __construct($config, $data)
     *
     * @param array конфиг
     * @param array record
     * @param bool verified = true if data load from DB, false = create new object
     */
    function __construct(
          model_collection_interface $container
        , $config = false
        , $data = false
        , $verified = false) {

        $inerface_check = 'model_collection_interface';

        if (!($container instanceof $inerface_check)) {
            throw new collection_exception('Bad constructor syntax', tf_exception::CRITICAL);
        }

        $this->db = $container->get_db();
        $this->set_container($container);

        $this->set_key($container->get_key());

        //
        // dummy object
        //
        if (!$config) {
            $this->is_dummy = true;
            $this->construct_dummy_after();
            return;
        }

        $this->config = $config instanceof collection_params
            ? $config
            : new collection_params($config);

        // get this from parent collection
        $this->set_working_fields($container->get_working_fields());

        $this->behaviors = new model_behaviors();
        $this->create_behaviors();

        $this->construct_before($data);

        // проверенная загрузка
        if ($verified) {

            $this->_is_new = false;

            if (!empty($data)) {
                $this->filter_data($data);
                $this->set_data($data);

                // from alloc()
                if (!empty($config['allocated'])) {
                    $this->_is_new       = true; //from alloc
                    $this->_is_allocated = true;
                }

                if ($this->get_key()) {

                    if (!$this->_is_new && !isset($data[$this->get_key()]))
                        throw new Collection_Exception('Create collection_item without ID [' . $this->get_key() . '], but verified flag specified');

                    $this->set_id($data[$this->get_key()]);
                }

            }
        } else if ($data !== false) {
            // newb! (создается новый)
            $this->create($data);
            $this->_is_new = false;
        }

        $this->make_urls();

        $this->construct_after();

        if ($deps = $this->with_deps()) {
            $this->load_secondary($deps);
        }
    }

    /**
     * @param $k
     */
    function set_key($k) {
        $this->_key = $k;
    }

    /**
     * @return string
     */
    function get_key() {
        return $this->_key;
    }

    /**
     * @return bool|false|object
     */
    function get_id() {
        return !$this->get_key()
            ? false
            : $this->get_data($this->get_key());
    }

    /**
     * @param $v
     * @return $this
     */
    function set_id($v) {
        if ($this->get_key()) {
            $this->set_data($this->get_key(), $v);
        }

        return $this;
    }

    /**
     * @todo Validate item fields
     */
    protected function validate() {
    }

    /**
     *  @todo Normalize input
     */
    protected function normalize(&$data) {
    }

    /**
     *
     */
    function create_behaviors() {
        $behaviors = $this->container->get_behaviors();
        if (!empty($behaviors)) {
            foreach ($behaviors as $bhID) $this->add_behavior($bhID);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    function add_behavior($id, $behavior = null) {

        if (!$behavior) {
            list($module, $behavior) = explode('.', $id);
            $class_fqcn = '\\SatCMS\\' . ucfirst($module) . '\\Classes\\Behaviors\\' . ucfirst($behavior);
             $behavior = new $class_fqcn;
        }

        $this->behaviors[$id] = $behavior ;
        $this->behaviors[$id]->set_model($this);

        return $this->behaviors[$id];
    }

    /**
     * @param $id
     * @return bool
     */
    function has_behavior($id) {
        return $this->behaviors->is_set($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    function behavior($id) {
        return $this->behaviors->get($id);
    }

    /**
     * function construct_before(&$data) {}
     * function construct_after() {}
     */

    /**
     * @return dbal
     */
    function connection() {
        return $this->db;
    }

    /**
     * Set context (abs_collection) container
     * used for querying
     *   valid fields
     *   exyta fields
     */
    /*protected*/
    function set_container(model_collection_interface $container) {
        $this->container = $container;
    }

    /**
     * return IAbs_Collection container
     */
    /*protected*/
    function get_container() {
        return $this->container;
    }

    /**
     * Get sql table
     */
    function get_table() {
        return $this->config->table;
    }

    /**
     * sql table prefix
     */
    function get_prefix() {
        return $this->config->get('prefix');
    }

    /**
     * Filter input data and save only needed key
     * Called on creation of item
     *
     * @see self::format_fields() onload
     */
    function filter_data(&$data) {

        if (empty($data) || !is_array($data)) return false;

        $container = $this->get_container();
        $d_keys    = $this->get_fields_keys();

        foreach ($data as $k => &$fld)
            if (!in_array($k, $d_keys) || is_numeric($k)) unset($data[$k]);
            else {
                // cast field
                $fld = $container->format_field($k, $fld, 'load');
            }


    }

    /**
     * Prepend item fields for SQL operation
     */
    function format_field_sql($key, $fld) {
        return $this->get_container()->format_field_sql($key, $fld);
    }

    /**
     * Reset internal cache
     */
    function drop_internal_cache() {
        $this->render_cache = array();
    }

    /**
     * Make sef name for item
     * @param string key
     * @param mixed spacing
     * @param array data
     * @return string alias
     */
    private function _unique_alias($name, $space, $data = null) {

        $alias = $data[$name];

        $where = $this->is_new() ? '' : ('id <> ' . $this->get_id() . ' AND ');

        // namespace for fields array(array(space,value), ...)
        if (!empty($space) && !is_array($space)) $space = array($space);

        if ($space) {
            $s_where = array();
            foreach ($space as $s)
                $s_where [] = ($s . ' = ' . $data[$s]);

            $s_where = implode(' AND ', $s_where);
            $where .= $s_where;
            $where .= ' AND ';
        }

        // check exists
        if (($t = $this->db->fetch_row(
                ($res = $this->db->query("SELECT count(*) as count FROM " . $this->get_table() . " WHERE " . $where . "{$name} = '{$alias}'"))
            )) && $t['count'] > 0
        )
            $alias .= '-' . uniqid();

        $this->db->free_result($res);

        return $alias;
    }

    /**
     * @deprecated @see self::_unique_alias
     * Make sef name for item
     */
    private function make_name(&$name, $space = false, $s = 1) {

        if (empty($name)) $fld = time() . rand(1, 9);
        else
            $name = functions::translit($name);

        $where = $this->is_new() ? '' : ('id <> ' . $this->id . ' AND ');

        // namespace for fields array(array(space,value), ...)
        if ($space) {
            $where .= ($space[0][0] . ' = ' . $space[0][1] . ' AND ');
        }

        // check exists
        if ($this->db->sql_numrows(
            ($res = $this->db->query("SELECT id FROM " . $this->get_table() . " WHERE " . $where . "name = '{$name}'"))
        )
        ) {
            $name .= rand(1, 9);
            $this->make_name($name, $space, ++$s);
        }

        $this->db->free_result($res);
    }

    /**
     * Set working fields for an item
     * Without params it clear weorking fields
     * Warn! use this only with UPDATE item
     * @param mixed varargs OR array
     * @return model_item
     */
    public function set_working_fields() {
        $this->working_fields = array();
        $count = func_num_args();

        if (!empty($count)) {

            $args = func_get_args();

            // array
            if ($count == 1 && is_array($args[0])) {
                $this->working_fields = $args[0];
            // ...varargs
            } else {
                foreach ($args as $item) {
                    $this->working_fields [] = $item;
                }
            }
        }

        return $this;
    }

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
     * Format all fields
     * Calls to container method
     * @param array data
     * @param string type {@see format_field}
     * @throws tf_exception
     */
    public function format_fields(&$data, $type = false) {

        if (!$type) {
            throw new collection_exception('Undefined format type');
        }

        if (!is_array($data)) {
            core::dprint_r($data);
            throw new collection_exception('Data wrong type: ' . gettype($data));
        }

        $container = $this->get_container();

        $vfs_keys = $this->get_fields_keys();

        foreach ($vfs_keys as $k) {

            if (!isset($data[$k])) $data[$k] = '';
            $v =& $data[$k];

            $current = $this->get_data($k);

            /* 
                Fields modificators:
                  + autosave
                  + default 
                  + make_seo
            */

            $skip = false;

            if ('modify' == $type) {

                if (!$this->in_working_set($k))
                    $skip = true;
                else
                    if ($vf = $this->field($k)) {

                        // make seo
                        if (isset($vf['make_seo']) && !empty($vf['make_seo'])) {

                            $with_translit = false;
                            $with_strict   = false;

                            if (is_array($vf['make_seo'])) {

                                if (isset($vf['make_seo']['key'])) {
                                    // new syntax: key, translit, strict (without spaces)
                                    $with_strict    = @$vf['make_seo']['strict'];
                                    $with_translit  = @$vf['make_seo']['translit'];
                                    $vf['make_seo'] = $vf['make_seo']['key'];
                                } else {
                                    $with_translit  = $vf['make_seo'][1];
                                    $vf['make_seo'] = $vf['make_seo'][0];
                                }

                                // fill url, if empty
                                if (empty($v) && $vf['make_seo'] && isset($data[$vf['make_seo']])) {
                                    $v = $data[$vf['make_seo']];
                                }
                            } else {
                                // oldstyle: make_seo = 1|true switch
                                $with_translit = true;
                            }

                            if ($with_translit && !empty($v))
                                $v = functions::translit($v);

                            // something bad trigger this code
                            if (empty($v)) $v = uniqid();
                            else {

                                $v = preg_replace('/[^а-яёa-z\-\_0-9[:space:]]/ui', '', $v);

                                if ($with_strict) {
                                    $v = preg_replace('/\s{1,}/u', '-', $v);
                                }

                                $v = strings::strtolower(trim($v));

                                // @todo fix name spaces
                                if (($this->is_new() || empty($v)) && !empty($data[$vf['make_seo']])) {
                                    $v = $this->_unique_alias($k, @$vf['space'], $data);
                                }
                            }
                        }

                        // autosave
                        if (isset($vf['autosave']) && $vf['autosave'] && !$this->is_new()) {
                            $data[$k] = $this->get_data($k);
                        }

                        // default
                        if (isset($vf['default']) && $this->is_new() && empty($data[$k])) {
                            $data[$k] = $vf['default'];
                            // for unixtime 'now' set, parsed in collection::on_modify to time()
                        }
                    }


            }

            if (!$skip)
                $v = $container->format_field($k, $v, $type, $current, $this);

        }
    }

    /**
     * Prepare data for edit (backend)
     * Assign data directry to template
     */
    function prepare2edt() {

        $data = $this->data;

        $this->format_fields($data, 'edit');

        $this->prepare2edt_before(reference::make($data));

        $form = array(
            'data'   => $data,
            'fields' => $this->fields(),
            'ctype'  => (($ctype = $this->get_ctype()) ? $ctype->render() : array())
        );

        $this->prepare2edt_after(reference::make($form));

        $this->_assign_tpl('form', $form);

        return $data;
    }

    /* prepare mutator 
    protected function prepare2edt_before(&$data) {}
    */

    /**
     * @param $k
     * @param $v
     * @deprecated do not use parser directly
     */
    protected function _assign_tpl($k, $v) {
        core::lib('renderer')->set_return($k, $v);
    }

    /**
     * Get data as array
     * @return array items as array
     */
    function as_array($fields = false) {

        if (!empty($fields) && is_string($fields)) {
            return $this->get_data($fields);
        }

        $data = $this->get_data($fields);

        $this->_filter_working_set($data);

        return $data;
    }

    /**
     * use $this->get_data(fields)
     * @param $data
     * @param bool $fields
     */
    protected function _filter_fields(&$data, $fields = false) {
        // @todo optimize, bad implementation
        if (!empty($fields)) {
            settype($fields, 'array');
            $data = array_intersect_key($data, array_flip($fields));

            // if one item, assign directly to return value
            if (count($fields) == 1) {
                $data = array_shift($data);
            }
        }
    }

    /**
     * Filter working set
     * @param $data
     * @param array $valid_keys
     * @return $this
     */
    protected function  _filter_working_set(&$data, $valid_keys = array('urls')) {

        if (!empty($this->working_fields)) {
            foreach ($data as $k => $v) {
                if ($k != $this->get_key() && !$this->in_working_set($k)
                    && !in_array($k, $valid_keys)
                    // && $this->check_vf_exists($k))
                ) unset($data[$k]);
            }
        }

        return $this;
    }

    /**
     * Convert to json
     * Respect 'json' => false in  model definition
     * @return array
     */
    function as_json($fields = false) {

        $data =  $this->render($fields);

        foreach ($this->fields() as $key => $field) {
            if (array_key_exists('json', $field) && !$field['json'] && array_key_exists($key, $data)) {
                unset($data[$key]);
            }
        }

        return $data;

    }

    /**
     * Remove
     * Physicaly remove from database
     * @return bool status
     */
    function remove() {

        if ($this->is_dummy) return;

        if (false === $this->remove_before()) {
            return false;
        }

        // load secondary, if not did it earlier
        $this->load_secondary(true);

        $data = $this->get_data();

        $this->format_fields($data, 'remove');

        if (!$this->get_key()) {
            throw new collection_exception('Cant delete without PK');
        }

        $sql = sprintf("DELETE FROM %s WHERE %s = %s LIMIT 1"
            , $this->get_table()
            , $this->get_key()
            , $this->format_field_sql($this->get_key(), $this->get_id())
        );

        $res = $this->db->sql_query($sql);

        $this->remove_after();

        return $res;
    }


    /** @return false cancel remove */
    /*
    function remove_before() {}
    function remove_after() {}
    */

    /**
     * Get tpl table
     */
    function get_tpl_table() {
        return $this->get_container()->get_tpl_table();
    }

    /**
     * @deprecated
     * Render to editor
     * Auto assign to template
     * @param bool default false
     * @param bool default false
     */
    function render2edt($fields = false) {
        return $this->render($fields);
    }

    /**
     * Get renderer cache
     * Call this method on child render()
     * and return if not empty
     */
    function get_render_cache() {
        return empty($this->render_cache) ? false : $this->render_cache;
    }

    /**
     * Render item to front
     * @param array $fields filter
     * @return array data
     */
    function render($fields = false) {

        if ($this->with_deps()) {
            $this->render_secondary();
        }

        /**
         * @see self::get_render_cache and use it on child::render
         * if (false !== ($data = $this->get_render_cache())) {
         * return $data;
         * }
         */

        /**
         * data must be an array
         */

        $data = array();

        // single field
        if (is_string($fields)) {
            $data[$fields] = $this->get_data($fields);
        } else {
            $data = $this->get_data($fields);
        }

        $ref_data = reference::make($data);

        $this->render_before($ref_data);

        $this->format_fields($data, 'view');

        if ($this->_with_render_cache) {
            $this->render_cache = $data;
        }

        $this->_filter_working_set($data);

        $this->_filter_fields($data, $fields);

        $this->render_after($ref_data); //Reference::make($data)

        return $data;
    }

    /*
    function render_before(&$data) {}
    function render_after(&$data) {}
    */

    function render_virtual($method, $type) {

        if ($method instanceof Closure) {
            return $method($this, $type);
        }

        $method = 'virtual_' . $method;

        if (!functions::is_callable(array($this, $method))) {
            throw new collection_exception('Virtual method not callable : ' . get_class($this) . '.' . $method);
        }

        return call_user_func(array($this, $method), $type);
    }

    /**
     * Create item
     * @return integer|bool newID of false
     */
    protected function create(array $data) {

        $ref_data = reference::make($data);

        $this->create_before($ref_data);
        $this->modify_before($ref_data);

        $_data = $data;

        // create new!
        $this->format_fields($data, 'modify');

        // filter          
        // $this->filter_data($data);          
        $this->set_data($data);

        // if everithing is alright, return new item id
        /*if ($this->save()) {
            $this->id = $this->data['id'] = $data_['id'] = $this->db->sql_nextid();              
            core::dprint('insert new ' . get_class($this) . ' #id ' . $this->id);                
            if (is_callable(array($this, 'modify_after'))) $this->modify_after($data_);                
        }
        else return false;
        */

        if (!$this->save()) {
            return false;
        }

        $data = $this->get_data();
        $this->filter_data($data);
        $this->set_data($data);

        // update ID
        $_data['id'] = $this->get_id();

        $this->create_after($_data);
        $this->modify_after($_data);

        return $this->get_id();
    }

    /*
    function create_before(&$data) {;}
    function create_after() {;}
    */

    /**
     * Load/create an item
     *
     * @param integer record#id (load from database)
     * @return mixed false or itemID if ok
     */
    private function load($id) {

        if ($this->is_dummy) return;

        // load
        core::dprint('#load id=' . $id);
        $key = $this->get_key();

        if (!$key) throw new collection_exception('item::load without key');

        $id = $this->format_field_sql($key, $id);

        $sql = "SELECT * FROM " . $this->get_table() . " WHERE id = " . $id;
        $res = $this->db->query($sql);
        $row = $this->db->fetch_row($res);
        $this->db->free_result($res);

        $this->filter_data($row);

        $this->set_data($row);
        $this->set_id($row[$this->get_key()]);

        return $this->get_id();
    }

    /**
     * Save to database
     * @return mixed nextID|bool
     */
    function save() {

        if ($this->is_dummy) return;

        $this->save_before();

        $res = false;

        $vfs = $this->fields();

        $low_priority = $this->is_delayed();

        $key              = $this->get_key();
        $is_autoincrement = $key && $this->is_key_autoincrement();

        /**
         * Update
         */
        if (!empty($this->id) && !$this->is_allocated()) {
            // modify
            $sql = "UPDATE " . ($low_priority ? 'LOW_PRIORITY ' : '') . $this->config->table . " SET";

            foreach ($vfs as $k => $v) {
                // skip this types of fields
                if ($k != 'id' && $v['type'] != 'virtual' && !isset($v['extra'])
                    && $this->in_working_set($k)
                ) {
                    if (!isset($this->data[$k])) $this->data[$k] = '';
                    $fld = $this->format_field_sql($k, $this->data[$k]);
                    $sql .= " $k = ";
                    $sql .= $fld;
                    $sql .= ',';
                }
            }
            $sql = substr($sql, 0, -1) . " WHERE id = " . $this->id;

            $res = $this->db->query($sql);

        } /**
         * Insert
         */
        else {
            // check for position
            $this->assign_position();

            // new
            $sql      = "INSERT " . ($low_priority ? 'DELAYED ' : '') . "INTO " . $this->config->table . " ";
            $sql_part = array(array(), array());
            foreach ($vfs as $k => $v) {

                if (!isset($this->data[$k])) $this->data[$k] = '';
                $no_skip = true;

                // skip this types of fields
                if (empty($this->data[$k]) && $v['type'] == 'timestamp'
                    || $k == 'id'
                    || $v['type'] == 'virtual'
                    || isset($v['extra'])
                )
                    $no_skip = false;

                if ($no_skip) {
                    $sql_part[0][] = $k;
                    $sql_part[1][] = $this->format_field_sql($k, $this->data[$k]);
                }
            }
            $sql .= "(" . implode(',', $sql_part[0]) . ") VALUES ";
            $sql .= "(" . implode(',', $sql_part[1]) . ");";

            $res = $this->db->query($sql);

            if ($this->is_allocated() && $res) {
                $this->container->append($this, $this->get_id());
                $this->_is_allocated = false;
            }

            // update ID
            if ($res) {

                if ($is_autoincrement) {
                    $res = $id = $this->db->sql_nextid();
                    $this->set_id($id);
                }
            }

            $this->_is_new = false;

        }

        $this->save_after($res);

        return $res;

    }

    /*
    function save_before() {}
    function save_after($res) {}
    */

    /**
     * Is this item new?
     */
    function is_new() {
        return $this->_is_new;
    }

    /**
     * Is this item allocated via @see Collection::alloc
     * @return $this|bool
     */
    function is_allocated($flag = null) {
        if (isset($flag)) {
            $this->_is_allocated = $flag;
            return $this;
        }

        return $this->_is_allocated;
    }

    /**
     * Make spacing sql
     * @return string where clause
     */
    private function make_space_sql($space) {
        if (is_array($space)) {
            $sql_tail_ = array();
            foreach ($space as $pos_space) $sql_tail_[] = "{$pos_space} = " . $this->get_data($pos_space);
            $sql_space = implode( /*','*/
                ' AND ', $sql_tail_);
        } else
            $sql_space = $space . " = " . $this->get_data($space);

        return $sql_space;
    }

    /**
     * Called when new item creates
     * Calc position for item and set it
     */
    private function assign_position() {
        $sql_tail = '';
        if (!$this->container->with_positions()) return;
        $pos = $this->field('position');
        if (isset($pos['space'])) {
            $sql_tail = ' WHERE ' . $this->make_space_sql($pos['space']);
        }
        $sql = "SELECT max(position) as mp FROM " . $this->get_table() . $sql_tail;
        $row = $this->db->fetch_row(($res = $this->db->query($sql)));
        $this->db->free_result($res);
        $this->position = (int)$row['mp'] + 1;
    }

    /**
     * modify item (submit)
     *
     * called on item update from (@see collection::modify)
     *
     * @return array data
     */
    function modify($data) {

        $this->modify_before(reference::make($data));

        $_data = $data;

        $this->format_fields($data, 'modify');
        unset($data[$this->get_key()]);

        // $this->filter_data($data);      

        $keys = $this->get_fields_keys();
        foreach ($keys as $key) {
            if (isset($data[$key]) && $this->in_working_set($key)) {
                $this->data[$key] = $data[$key];
            }
        }

        // restore key
        $_data[$this->get_key()] = $data[$this->get_key()] = $this->data[$this->get_key()] = $this->get_id();
        core::dprint('modify ' . get_class($this) . ' #id ' . $data[$this->get_key()]);

        // save to db
        $this->save();

        // working fields set deny modifying extra data,
        // only with full update

        if (empty($this->working_fields)) {
            $this->modify_after($_data);
        } else {
            $this->modify_partial_after($_data, $this->working_fields);
        }

        // remove renderer and other cache
        $this->drop_internal_cache();

        // call onload         
        $data = $this->get_data();
        $this->filter_data($data);
        $this->set_data($data);

        return $data;

    }

    /**
     * Autocalls on most bottom of "modify"
     * We already has new item ID in data
     */

    // function modify_after($data) {}

    /**
     * Autocalls on most bottom of "modify"
     * We already has new item ID in data
     */

    // function modify_before(&$data) {}


    /**
     * Dump iatems (debug)
     */
    function dump() {
        core::dprint('Dump of ' . get_class($this) . ', id ' . $this->get_id());
        core::dprint(print_r($this->as_array(), 1));
    }

    /**
     * Загрузка вспомогательных элементов
     * (дочерние объекты, зависимые объекты...)
     * Реализовывается в дочерних классах
     */
    function load_secondary($options = null) {
        $this->load_secondary_after($options);
        return $this;
    }

    function render_secondary() {
        $this->render_secondary_after();
        return $this;
    }

    /**
     * @desc format with
     * @throws collection_exception
     */
    function __get($index) {

        // @fixme: protected are undefined
        if ('id' == $index) return $this->get_id();
        if ('data' == $index) return $this->data;

        if (array_key_exists($index, $this->data)) {
            return $this->data[$index];
        } else {
            // try with reflection (for private, protected)
            // return false, if real property exists
            $reflect = new ReflectionObject($this);
            try {
                $reflect->getProperty($index);

                return false;
            } catch (ReflectionException $e) {
                throw new collection_exception('try to get undefined index `' . $index . '` data: ' . print_r($this->data, 1));
            }
        }

        return false;
    }

    /**
     * @desc
     */
    function __set($index, $val) {
        $this->drop_internal_cache();
        $this->data[$index] = $val;
    }

    /**
     * @desc
     */
    function __isset($index) {
        return isset($this->data[$index]);
    }

    /**
     * Make urls for item
     * Child item must provide self urls list
     * use @see self::append_urls
     */
    protected function make_urls() {
        $this->make_urls_after();
    }

    /**
     * Append urls in list
     */
    public function append_urls($name, $url) {
        if (false === $this->get_data('urls')) $this->urls = array();
        $this->urls = array_merge($this->urls, array($name => $url));
    }

    /**
     * Use "self" for item url
     */
    function get_url($id = null) {
        $urls = $this->get_data('urls');

        return $id ? @$urls[$id] : $urls;
    }

    /**
     * Set/get delayed insert flag
     * Sqlite does not support DELAYED queries
     */
    public function is_delayed() {
        return $this->get_container()->is_delayed();
    }

    /**
     * Update item field/s indirect
     * @param array data
     *   1) 'param' => $value  - indirect
     *   2) 'param1', 'param2' - directly from item
     */
    function update_fields($data) {

        if (!is_array($data) && func_num_args() > 0) {
            $keys = func_get_args();
            $data = array();
            foreach ($keys as $v) {
                $data[$v] = $this->get_data($v);
            }
        } else
            //  array('field', ...)
            if (is_array($data) && isset($data[0])) {
                $keys = $data;;
                $data = array();
                foreach ($keys as $v) {
                    $data[$v] = $this->get_data($v);
                }
            }

        if ($this->is_dummy) return;

        $this->container->update_item_fields($this->id, $data);
    }


    /**
     * __call cant pass args by ref
     * public mixed __call ( string $name , array $arguments )
     */
    function __call($method, $params) {

        // delegate to collection
        if (in_array($method, self::$_delegate_methods)) {
            return call_user_func_array(array($this->container, $method), $params);
        }

        // behaviors delegates
        if (preg_match('@(before|after)$@', $method)) {

            if ($this->behaviors && !$this->behaviors->is_empty()) {
                $this->behaviors->invoke($method, $params);
            }

            // allow empty events
            return;
        }

        throw new collection_exception('__Called bad method: ' . $method);

    }

}
