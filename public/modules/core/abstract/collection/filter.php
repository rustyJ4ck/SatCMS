<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection_filter.php,v 1.8.2.1.2.2 2012/10/18 06:59:58 Vova Exp $
 */

/**
 * Filter result
 */
class collection_filter_result extends aregistry {
}

/**
 * Collection filter
 * usage @see core::create_filter
 */
class collection_filter {

    const CONNECTOR_AND = 'AND';
    const CONNECTOR_OR  = 'OR';
    const FILT_RAW      = true;

    public static $operators = array(
        '='      => 1,
        '>='     => 1,
        '<='     => 1,
        '>'      => 1,
        '<'      => 1,
        'LIKE'   => false,      // closures //
        'BEGINS' => false,
        'ENDS'   => false,
        'BETWEEN'   => false,
    );
    protected static $connectors = array(
        'AND' => 1
      , 'OR'  => 1
    );
    /**
     * @var collection_params
     */
    public $config = array();
    /** @var abs_collection */
    protected $collection;
    protected $count;
    protected $pagination_start = 0;
    protected $pagination_limit = 10;
    protected $base_url = '/';
    protected $url_template = 'page/{page}/';

    protected $filters = array();
    protected $orders;

    protected $_filters = array();

    // internal pagination
    protected $pagination;

    protected $template;
    protected $_no_count = false;
    protected $render_with_meta = false;

    protected $_with_render = false;

    /**
     * @var bool clear collection internal condition (where,order,..etc)
     */
    protected $_with_clear = true;

    /**
     * Build filter
     * @param IAbs_Collection object
     * @param string base url
     */
    function __construct(IAbs_Collection $collection, $base_url = false) {

        $this->set_collection($collection);

        if (false !== $base_url)
            $this->base_url = $base_url;

        if (core::in_editor())
            $this->url_template = "&start={page}";

        $this->config = new collection_params($this->config ? $this->config : null);

        $this->configure();

    }

    /**
     * Configure stuff
     */
    function configure() {

        // 5.3 this in closure
        $self = $this;

// LIKE
// ---------------------------------------------------

        if (false === static::$operators['LIKE'])
            static::$operators['LIKE'] = function ($v) use ($self) {

                $v['value'] = trim(strings::str_replace('%', '', $v['value']));
                if (empty($v['value'])) return false;

                return $v['key'] . ' LIKE ' . $self->_escape_field($v['key'], '%' . $v['value'] . '%');
            };

// BEGINS
// ---------------------------------------------------

        if (false === static::$operators['BEGINS'])
            static::$operators['BEGINS'] = function ($v) use ($self) {

                $v['value'] = trim(strings::str_replace('%', '', $v['value']));
                if (empty($v['value'])) return false;

                return $v['key'] . ' LIKE ' . $self->_escape_field($v['key'], $v['value'] . '%');
            };

// ENDS
// ---------------------------------------------------

        if (false === static::$operators['ENDS'])
            static::$operators['ENDS'] = function ($v) use ($self) {

                $v['value'] = trim(strings::str_replace('%', '', $v['value']));
                if (empty($v['value'])) return false;

                return $v['key'] . ' LIKE ' . $self->_escape_field($v['key'], '%' . $v['value']);
            };

// BETWEEN
// ---------------------------------------------------

        if (false === static::$operators['BETWEEN'])
            static::$operators['BETWEEN'] = function ($v) use ($self) {

                if (empty($v['value']) || !is_array($v['value'])) return false;

                $from = $self->_normalize_value($v['key'], $v['value']['from']);
                $to = $self->_normalize_value($v['key'], $v['value']['to']);

                // if ($from === $to) return false;

                $sql = $self->_sql_between($v['key'], $from, $to);

                if ($sql) {
                    return $v['key'] . ' ' . $sql;
                }

                return false;
            };

        /*
        'value' =>
            array (size=2)
              'from' => string '18.04.2014 03:06' (length=16)
              'to' => string '18.04.2014 03:06' (length=16)
          'operator' => string 'BETWEEN' (length=7)
          'connector' => string 'AND' (length=3)
          'raw' => null
        */

    }

    /**
     * Get collection
     * @return abs_collection
     */
    function get_collection() {
        return $this->collection;
    }

    function set_collection($collection) {
       $this->collection = $collection;
    }

    /**
     * @param $config
     * @return $this
     */
    function set_config($config) {
        if (is_array($config)) {
            $this->config = new collection_params($this->config ? $this->config : null);
        } else {
            $this->config = $config;
        }

        return $this;
    }

    /**
     * Set up base url
     */
    function set_base_url($url) {
        $this->base_url = $url;

        return $this;
    }

    /**
     * Set pagination
     * @param integer page
     * @param integer per page
     */
    function set_pagination($start, $limit = null) {
        if ($limit) $this->set_per_page($limit);
        $this->pagination_start = (int)$start;

        return $this;
    }

    /**
     * Set per page limit
     */
    function set_per_page($int) {
        $this->pagination_limit = $int;

        return $this;
    }

    /**
     * Reset filter
     */
    function reset() {
        $this->filters = array();
        $this->orders  = array();

        return $this;
    }

    function &get_filter($key) {
        $tmp = null;
        if (is_array($this->filters) && array_key_exists($key, $this->filters)) $tmp = & $this->filters[$key];

        return $tmp;
    }

    /**
     * @param $key
     * @return pointer
     */
    function &get_order($key) {
        $tmp = null;
        if (is_array($this->orders) && array_key_exists($key, $this->orders)) $tmp = & $this->orders[$key];

        return $tmp;
    }

    function unset_filter($key) {
        if (isset($this->filters[$key])) unset($this->filters[$key]);

        return $this;
    }

    function unset_order($key) {
        if (isset($this->orders[$key])) unset($this->orders[$key]);

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return bool|string
     */
    function _sql_like($key, $value) {

        if (empty($value)) return false;

        return ' LIKE ' . $this->_escape_field($key, '%' . $value);
    }

    /**
     * @param $key
     * @param $from
     * @param $to
     * @return string
     */
    function _sql_between($key, $from, $to) {

        $infinite = empty($to);
        $from = $this->collection->format_field_sql($key, $from);
        $to   = $this->collection->format_field_sql($key, $to);

        if (empty($from) && empty($to)) return;

        if ($from === $to) {
            $sql = "= {$from}";
        } elseif ($infinite) {
            $sql = "> {$from}";
        } else {
            $sql = "BETWEEN {$from} AND {$to}";
        }

        return $sql;
    }

    /**
     * 0..
     * array(FROM, TO)
     */
    function set_between_filter($k, $v, $op = self::CONNECTOR_AND) {

        if (!is_array($v) || count($v) != 2) throw new collection_filter_exception('Bad between');

        $from = array_shift($v);
        $to   = array_pop($v);

        $infinite = empty($to);

        $form = $this->collection->format_field_sql($k, $from);
        $to   = $this->collection->format_field_sql($k, $to);

        // skip empty values
        if (empty($form) && empty($to)) return;

        if ($from == $to) {
            $sql = "= {$from}";
        } elseif ($infinite) {
            $sql = "> {$from}";
        } else {
            $sql = "BETWEEN {$from} AND {$to}";
        }

        return $this->set_filter($k, $sql, $op, self::FILT_RAW);

    }

    /**
     * Set filter
     * @param $key
     * @param mixed $value           sql, if raw
     * @param string $operator
     * @param string $connector
     * @param bool $raw
     * @return $this
     */
    function set_filter($key, $value = null, $operator = '=', $connector = self::CONNECTOR_AND, $raw = false) {

        $this->filters[$key] = array(
            'value'   => $value
            , 'operator'  => $operator
            , 'connector' => $connector
            , 'raw'       => $raw
        );

        return $this;
    }

    /**
     * like (var%)
     */
    function set_like_filter($key, $value, $op = self::CONNECTOR_AND, $format = '%s%%') {
        if (false !== $key) {
            if (!empty($value))
                $this->filters[$key] = array('value' => 'LIKE \'' . sprintf($format, $this->collection->get_db()->escape($value)) . '\'', 'op' => $op, 'raw' => true);

            $this->filters[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $filters [filter => [1,2,3], 'order' => 1,2,3]
     */
    function set($filters = array()) {
        $this->set_filters(@$filters['filter']);
        $this->set_orders(@$filters['order']);

        return $this;
    }

    function set_filters($data) {

        $this->filters = array();

        if (!empty($data)) {
            foreach ($data as $k => $f)
                if (empty($f['raw'])) // unset raw
                {
                    $f['connector'] = @$f['connector'] ? : 'AND';
                    $f['operator']  = @$f['operator'] ? : '=';
                    $f['value']     = @$f['value'] ? : '';

                    $this->set_filter($k, $f['value'], $f['operator'], $f['connector']);
                }
        }

        return $this;
    }

    function set_orders($array) {
        $this->orders = array();
        if (!empty($array)) {
            foreach ($array as $k => $sort) $this->set_order($k, $sort);
        }

        return $this;
    }

    /**
     * Set order
     * Empty value skipped
     * (append)
     * @param string KEY
     * @param string ASC|DESC
     */
    function set_order($key, $value = 'ASC') {
        if (empty($key)) {
            $this->orders = false;
        } elseif (!empty($value)) {

            if (!is_scalar($value)) {
                throw new Collection_Filter_Exception('Bad order value');
            }

            $value              = strtoupper($value) == 'ASC' ? 'ASC' : 'DESC';
            $this->orders[$key] = $value;
        }

        return $this;
    }

    /**
     * Save to session
     * @param $key
     * @return $this
     */
    function save($key) {
        \Session::put('filters.' . $key, ($this->get()));

        return $this;
    }

    // key => [value, type, op]

    /**
     * Get all
     * @return array
     */
    function get() {
        return array('filter' => $this->filters, 'order' => $this->orders);
    }

    function forget($key) {
        \Session::forget('filters.' . $key);

        return $this;
    }

    /**
     * Doesnot reset filters if no session data
     * Load from session
     * @param $key
     * @return mixed
     */
    function load($key) {
        $key = 'filters.' . $key;
        if (\Session::has($key)) {
            $data          = \Session::get($key);
            $this->filters = $data['filter'];
            $this->orders  = $data['order'];

            return $data;
        }

        return false;
    }

    /**
     * Do not count items if number already known
     */
    function set_count($c) {
        $this->count     = $c;
        $this->_no_count = true;

        return $this;
    }

    /**
     * Assign tpl var on apply
     * @param $fl
     * @return $this
     */
    function with_render($fl) {
        $this->_with_render = $fl;
        return $this;
    }

    /**
     * Only count
     */
    function apply_count() {

        $this->_apply();

        return $this->count;
    }

    /**
     * Clear before apply
     * @param bool $flag
     */
    function with_clear($flag = true) {
        $this->_with_clear = $flag;
        return $this;
    }

    /**
     * Apply filters (internal)
     */
    protected function _apply() {

        if ($this->_with_clear) {
            $this->collection->clear(1);
        }

        // prepend config
        // make shure no "limit sql" in it!

        $config = $this->config;

        // where sql prepare

        /*
        if (isset($config['where_sql'])) {
            $where_sql_ = $this->collection->cfg('where_sql');
            if (!empty($where_sql_)) {
                $config['where_sql'] = $where_sql_ . ' AND ' . $config['where_sql'];
            }
        }
        */

        // map filter config to collection
        if (!$config->is_empty())
            foreach ($config as $k => $v) {
                $this->collection->config->set($k, $v);
            }

        // apply user filters
        $this->collection_filters();

        $this->count     = $this->_no_count ? $this->count : $this->collection->count_sql();
        $this->_no_count = false;

        core::dprint('[FILTER] Apply for ' . $this->collection->get_name() . ' x ' . $this->count);
    }

    /**
     * Build filters
     */
    protected function collection_filters() {

        /*
           'value' =>
            array (size=2)
              'from' => string '18.04.2014 03:06' (length=16)
              'to' => string '18.04.2014 03:06' (length=16)
          'operator' => string 'BETWEEN' (length=7)
          'connector' => string 'AND' (length=3)
          'raw' => null
         */

        //
        // Filters: Conditions
        //

        if (!empty($this->filters)) {
            foreach ($this->filters as $k => $v) {

                $k   = $this->_sql_key($k);
                $sql = false;

                if ($k && array_key_exists($v['connector'], static::$connectors)
                    && array_key_exists($v['operator'], static::$operators)
                ) {

                    if ($v['raw']) {

                        // @todo RAW
                        $sql = strings::str_replace(
                            array(':connector', ':key', ':operator', ':value'),
                            array($v['connector'], $k, $v['operator'], $this->_normalize_value($k, $v['value'])),
                            $v['raw']
                        );

                    } else {

                        if (!isset(static::$operators[$v['operator']])) {
                            throw new collection_filter_exception(__METHOD__ . ' no operator ' . $v['operator']);
                        }

                        if (static::$operators[$v['operator']] instanceof Closure) {
                            $v['key'] = $k;
                            $sql      = call_user_func(static::$operators[$v['operator']], $v);
                        } else {
                            if (!empty($v['value']))
                                $sql = $k . ' ' . $v['operator'] . " " . $this->_normalize_value($k, $v['value']);
                        }
                    }

                    if ($sql) {
                        $this->collection->append_where($sql, $v['connector']);
                    }
                }
            }
        }

        //
        // Sorting
        //

        if (!empty($this->orders)) {
            $orders = array();
            foreach ($this->orders as $k => $v) {
                $v = strtoupper($v);

                if ('ASC' !== $v && 'DESC' !== $v) {
                    throw new Collection_Filter_Exception('Bad order: ' . $k);
                }

                $orders [] = $this->_sql_key($k) . ' ' . $v;
            }

            $this->collection->set_order(
                trim(join(', ', $orders))
            );
        }

    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    protected function _escape_field($name, $value) {
        return $this->collection->format_field_sql($name, $value);
    }

    /**
     * @param $k
     * @throws collection_filter_Exception
     */
    protected function _sql_key($k) {

        $key = preg_replace('@[^\w_]@', '', $k);

        if (empty($key)) {
            throw new collection_filter_Exception('Bad key: ' . $k);
        }

        return $key;
    }

    /**
     * @Todo this is dirty!
     * @param $key
     * @param $value
     * @return string
     * @deprecated use @see $this->_escape_field
     */
    function _normalize_value($key, $value) {

        if ($this->collection && ($field = $this->collection->field($key))) {

            switch ($field['type']) {

                case 'datetime':
                case 'unixtime':
                case 'timestamp':
                    $value = strtotime($value);
                    break;

                case 'numeric':
                case 'relation':
                    $value = intval($value);
                    break;

                case 'text':
                default:
                    $value = $this->_escape($value);
                    break;
            }
        } else {
            throw new collection_filter_exception('_normalize_value : ' . $key);
        }

        return $value;
    }

    /**
     * @param $value
     * @return array
     */
    protected function _escape($value) {
        return $this->collection->connection()->escape($value);
    }

    /**
     * Apply filter sets
     *
     * Remember: in editor data auto rendered thru render2edt()!
     *
     * @throws collection_filter_exception
     *
     * @return object {pagination, filters, data}
     */
    function apply($need_render = false) {
        $this->apply_filters();

        $result = $this->render();

        if ($need_render || $this->_with_render) {
            core::lib('renderer')->return->filter = $result;
        }

        return $result;

        /*
        $data = ($this->render_with_meta || $with_meta)
            ? $this->render2edt()
            : $this->render();

        return $data;
        */
    }

    private $on_apply_callback;

    /**
     * @param callable $callback onload(function($collection){})
     * @return $this
     */
    function on_apply(Closure $callback) {
        $this->on_apply_callback = $callback;
        return $this;
    }

    /**
     * Apply without render
     */
    function apply_filters() {

        $this->_apply();

        if ($this->pagination_limit * $this->pagination_start > ($this->count + $this->pagination_limit)) {
            throw new collection_filter_exception('Invalid range ' . $this->pagination_limit * $this->pagination_start . ' > ' . $this->count);
        }

        // real page 0=1, 2...
        $real_page = $this->pagination_start > 1 ? $this->pagination_start - 1 : 0;
        $this->collection->set_limit($this->pagination_limit, ($real_page * $this->pagination_limit));

        $this->collection->load();

        // Run callback
        if ($this->on_apply_callback instanceof Closure) {
            call_user_func($this->on_apply_callback, $this->collection);
            $this->on_apply_callback = null;
        }

        // generate_pagination
        $this->pagination = $this->paginate(
            $this->base_url,
            $this->count,
            $this->pagination_limit, // per page
            $this->pagination_start);

        return $this;
    }

    /**
     * Generate pagination array
     * Thx for ideas to mzz pager
     * @param string url with trailing '/'
     * @param integer
     * @param integer
     * @param integer current page
     * @return array [pagination]=array({'start', 'url', 'current}) or [] if fail
     */
    function paginate($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = true, $bulk_items = 7, $breverse = false) {

        $url_template = $base_url . $this->url_template;

        $result = array(
            'pagination' => array()
        );

        $pagination = & $result['pagination'];

        $pages_total = ceil($num_items / $per_page);;

        if ($pages_total == 1) return $result;

        if ($start_item <= 0) {
            $start_item = $breverse ? $pages_total : 1;
        }

        $reverse = $breverse ? -1 : 1;

        if ($num_items > 0) {
            $first              = $breverse ? $pages_total : 1;
            $pagination[$first] = array('start' => $first, 'url' => $this->_psprintf($url_template, $first));

            $left_skip  = ($breverse ? $pages_total - $start_item - 1 : $start_item - 2) > $bulk_items;
            $right_skip = ($breverse ? $start_item - 2 : $pages_total - $start_item - 1) > $bulk_items;

            if ($left_skip) {
                $pagination [] = array(); // spacer
                $left          = $start_item - $reverse * $bulk_items;
            } else {
                $left = $first + $reverse;
            }

            if ($right_skip) {
                $right = $start_item + $reverse * $bulk_items;
            } else {
                $right = $breverse ? 1 : $pages_total;
            }


            while ($reverse * ($right - $left) >= 0) {
                $pagination[$left] = array('start' => $left, 'url' => $this->_psprintf($url_template, $left));
                $left += $reverse;
            }

            if ($right_skip) {
                $pagination []     = array(); // spacer
                $last              = abs($first - $pages_total) + 1;
                $pagination[$last] = array('start' => $last, 'url' => $this->_psprintf($url_template, $last));
            }

            if (isset($pagination[$start_item])) {
                $pagination[$start_item]['current'] = true;
            }

            // set first url to base_url
            $pagination[$first]['url'] = $base_url;

            if ($add_prevnext_text && $pages_total > 1) {
                if ($start_item > 1) {
                    $result['prev'] = array('start' => $start_item - 1, 'url' => $this->_psprintf($url_template, $start_item - 1));

                }
                if ($start_item < $pages_total) {
                    $result['next'] = array('start' => $start_item + 1, 'url' => $this->_psprintf($url_template, $start_item + 1));
                }
            }

        }

        $result['current']   = $start_item;
        $result['count']     = $pages_total;
        $result['num_items'] = $num_items;
        $result['per_page']  = $per_page;

        return $result;
    }

    private function _psprintf($tpl, $page) {
        return str_replace('{page}', $page, $tpl);
    }

    /**
     * Render filter
     * @return object {data, pagination}
     */
    function render() {
        $result = new collection_filter_result();

        $data = ($this->render_with_meta)
            ? $this->collection->render2edt(true)
            : $this->collection->render();

        $result->collection = $data;

        $result->pagination = $this->pagination;

        $result->filters = $this->render_filters();
        $result->orders  = $this->get_orders();

        if (core::is_debug()) {
            $result->sql = $this->collection->get_last_query();
        }

        // if template specified, render it
        if ($this->template) {
            $result->data = \View::make($this->template, array('data' => $result->data))->render();
        }

        return $result;
    }

    /**
     * Remove raw from filters
     * @return mixed
     */
    function render_filters() {
        $filters = $this->filters;

        if (!empty($filters))
            foreach ($filters as $k => &$v) {
                if (isset($v['raw'])) unset($v['raw']);
            }

        return $filters;
    }

    function get_orders() {
        return $this->orders;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    function is_render_with_meta($flag = false) {
        $this->render_with_meta = $flag;

        return $this;
    }

    /**
     * Direct rendering
     * (editor)
     * Render collection
     * and
     * filter:
     *   pagination
     *   filters
     *
     * @deprecated
     */
    function render2edt() {

        core::lib('tpl_parser')->assign('filter', array(
            'pagination' => $this->pagination
        , 'filters'      => $this->get_filters()
        ));

        return $this->collection->render2edt();
    }

    /**
     * Get filters
     */
    function get_filters() {
        return $this->filters;
    }

}


  