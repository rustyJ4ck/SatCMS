<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: editor_controller.php,v 1.8.2.4.2.14 2013/05/15 07:19:31 Vova Exp $
 */

/**
 * Editor controller
 *
 * @property tf_auth                     $auth
 * @property tf_request                  $request
 * @property tf_renderer                 $renderer
 *
 * @property core                        $core
 */
abstract class editor_controller {

    /** @var core_module */
    protected $context;

    /** @var request_params */
    protected $params;

    /**
     * @var bool collection|controller
     */
    protected $with_model = true;

    protected $model_deps = false;

    /**
     * Controlled collection
     * set it to false, when collection disabled
     * @var model_collection
     */
    protected $collection;

    protected $collection_model;
    protected $collection_config = array();

    protected $filter_config;

    protected $grid_name;

    /** @var  array grid params */
    protected $grid_filters;
    protected $grid_sortables;

    protected $is_submitted = false;

    /** @var string save|apply  */
    protected $submit_type;

    protected $postdata;

    /** main template: index */
    protected $template;

    protected $layout = 'root';

    protected $title;

    protected $base_url;

// operation status
    private $_message = '';
    private $_status = true;
    private $_message_data;
    private $_redirect;

    private $_in_ajax;

    /** @var  silent mode */
    private $_disable_render;

    protected $_where = '';
    protected $_order = '';
    protected $_limit = '';

    protected $mode = 'list'; // form

    /** @var  aregistry controller data  */
    protected $response;

    const SUBMIT_CONTROL = 'form-submit';

    /** Construct */

    public function __construct($module /*$cdata, $config*/) {

        $this->response = new aregistry();

        $this->_in_ajax = loader::in_ajax();

        $this->context  = $module;

        $this->params   = $this->request->get_params();
        $this->postdata = $this->request->filespost();

        $this->base_url = $this->context->get_editor_base_url();

        // action, colection_model (%module%_%action%_controller)

        // pre-init
        $this->construct_before();

        $this->init_mode();

        if ($this->with_model()) {

            /**
             * Force load deps when edit item
             */
            if ($this->mode == 'form') {
                $this->model_deps = true;
            }

            $this->collection_model = empty($this->collection_model)
                ? substr(
                    substr(get_class($this), strlen($this->context->get_name()) + 1)
                    , 0, -11
                )
                : $this->collection_model;


            if (!isset($this->grid_name)) {
               $this->grid_name = 'grid-' . $this->params->m . '-' . $this->params->c;

               /*
               // @todo make grids unique ids
               if ($this->request->postget('grid')) {
                . '-' . functions::url_hash(microtime(1)); //str_replace('_', '-', $this->collection_model);
               }
               */
            }

            if ($this->collection !== false)
                $this->collection = $this->create_collection();
            else
                $this->collection = model_collection::get_null_collection();

            if ($this->model_deps) {
                $this->collection->with_deps($this->model_deps);
            }

            // apply editor model style
            $this->set_collection_format($this->get_mode());

            $this->get_grid_filters();
            $this->prepare_grid_filters();

        }

        // is item submitted?
        $this->is_submited = (bool)$this->request->post(self::SUBMIT_CONTROL);

        // op=modify old compat
        if ($this->is_submited) {
            $this->params->op = 'modify';
            $this->submit_type = $this->request->post(self::SUBMIT_CONTROL);
        }

        // post-init
        $this->construct_after();
    }

    /**
     * Prepare filters from cookie|request
     */
    protected function get_grid_filters() {

        $filters = @json_decode(substr(stripcslashes($this->request->cookie('grid-filters')), 1, -1), true);
        $this->grid_filters = $filters && @$filters[$this->grid_name] ? $filters[$this->grid_name] : array();

        // populate from post
        if (!empty($this->postdata['filter'])) {
            $this->grid_filters = functions::array_merge_recursive_distinct($this->grid_filters, $this->postdata['filter']);
        }

        $sortables =  @json_decode(substr(stripcslashes($this->request->cookie('grid-sortables')), 1, -1), true);
        $this->grid_sortables = $sortables && @$sortables[$this->grid_name] ? $sortables[$this->grid_name] : array();

    }

    /**
     * Stored filters come in cookie
     */
    protected function prepare_grid_filters() {

        // import filters from params
        foreach ($this->collection->fields() as $key => $field) {
            if (isset($field['filter']) && $this->params->is_set($key) && !array_key_exists($key, $this->grid_filters)) {
                $this->grid_filters[$key] = $this->params->get($key);
            }
        }

        // grid-filters
        if (!empty($this->grid_filters)) {
            foreach ($this->grid_filters as $key => $value) {

                if (1 /*!$this->params->offsetExists($key) && !$this->params->is_set($key)*/) {
                    // populate params, if not set
                    $this->params->set($key, $value);
                }
            }

            if (isset($this->grid_filters['limit'])) {
                $this->_limit = (int)$this->grid_filters['limit'];
            }
        }

    }

    /** @return model_collection */
    protected function create_collection() {
        return $this->context->model(
            $this->collection_model, $this->collection_config
        );
    }

    /**
     * Controller mode
     */
    protected function init_mode() {
        if ($this->with_model()) {
            if ($this->params->op != 'new' && $this->params->op != 'edit') {
                $this->mode = 'list';
            }
            else {
                $this->mode = 'form';
            }
        } else {
            $this->mode = $this->params->op;
        }
    }

    /**
     * @param $item
     * @return string
     */
    function get_edit_url(model_item $item, $extra = '') {
        return $this->context->editor->make_url(
            '?m=' . $this->params->m .
            '&c=' . $this->params->c .
            '&op=edit' .
            '&id=' . $item->id .
            '&pid=' . $this->params->pid .
            ($extra ? ('&' . $extra) : '') .
            '&start=' . $this->params->start
            , true
        );
    }

    /**
     * Set current collection
     */
    function set_collection(model_collection $c) {
        $this->collection = $c;

        return $this;
    }

    /**
     * @param $format
     * @return $this
     */
    function set_collection_format($format) {

        if (!$this->with_model()) return;

        $base_format = 'editor';

        $format = $base_format . '.' . $format;


        if ($this->collection->has_format($format)) {
            $this->collection->set_format($format);
        }

        /*
        // @fixme prevent 'editor' base format
        } elseif ($this->collection->has_format($base_format)) {
            $this->collection->set_format($base_format);
        }
        */

        return $this;

    }

    /**
     * Viewport template
     * @param $template
     * @return $this
     */
    function set_template($template) {
        $this->template = $template;

        return $this;
    }

    function get_template() {
        return $this->template;
    }

    /**
     * Layout template
     * @param $l
     * @return $this
     */
    function set_layout($l) {
        $this->layout = $l;

        return $this;
    }

    function get_layout() {
        return $this->layout;
    }

    function get_title() {
        return $this->title;
    }

    function get_context() {
        return $this->context;
    }

    function get_postdata() {
        return $this->postdata;
    }

    function disable_render($f) {
        $this->_disable_render = $f;

        return $this;
    }

    function in_ajax() {
        return $this->_in_ajax;
    }

    function set_where($sql) {
        $this->_where = $sql;

        return $this;
    }

    function set_order($sql) {
        $this->_order = $sql;

        return $this;
    }

    function set_limit($count) {
        $this->_limit = $count;

        return $this;
    }

    function get_base_url() {
        return $this->base_url;
    }

    /**
     * @return editor_controller
     */
    function set_base_url($url) {
        $this->base_url = $url;

        return $this;
    }

    /**
     * Build url for editor action
     * @param $u
     * @return mixed
     */
    function make_url($u) {
        return core::lib('editor')->make_url($this->base_url . '&' . $u, true);
    }

    protected function run_before() {
    }

    protected function run_after() {
    }

    /** run action (entry pooint) */

    function run($action = null) {

        if (preg_match('/[^\w_-]/', $this->params->op)) {
            throw new controller_exception(__METHOD__ . ': Bad action');
        }

        $action = $action ?: $this->params->op;
        $action = $action ?: 'index';

        $this->run_before();

        $this->render_controller();

        $this->action($action);

        if (!$this->_disable_render) {
            $this->render();
        }

        $this->renderer->set_page_title($this->title);

        $this->run_after();

        // visible in templates as {$controller.key}
        $this->renderer->set_data('controller', $this->response->as_array());

    }

    /**
     * Render {$controller} stuff
     */
    function render_controller() {
        $this->response->mode = $this->get_mode();
        $this->response->grid_name = $this->grid_name;
    }

    /**
     * controller mode
     * @return string
     */
    function get_mode() {
       return $this->mode;
    }

    function with_model() {
        return $this->with_model;
    }

    /*
    protected function action_before($op = null) {}
    protected function action_after($op = null) {}
    */

    /**
     * Run action
     * @param object operation
     */
    public function action($op, $data = null) {

        if (empty($op)) {
            throw new controller_exception('Empty action');
        }

        if (is_callable(array($this, 'action_before')))
            $this->action_before($op);

        $method = 'action_' . $op;

        core::dprint("run_action $method ");

        $response = null;

        if (method_exists($this, $method)) {
            $response = call_user_func(array($this, $method));
        }

        if (is_callable(array($this, 'action_after')))
            $this->action_after($op);

        if ($response instanceof Symfony\Component\HttpFoundation\Response) {
            $response->send();
            // thats bad!
            $this->core->halt();
            return;
        }

        // delayed ajax answer
        if ($this->_ajax_answer_data) {
            $this->ajax_answer(
                $this->_ajax_answer_data['status']
                , $this->_ajax_answer_data['message']
                , $this->_ajax_answer_data['data']
            );

            return; // die here
        }

        if ($this->_message) {

            // @todo really, this is not obvious

            if ($this->in_ajax()) {
                $this->ajax_answer(
                    /*$this->_status,
                    $this->_message,
                    $this->_message_data*/
                );
            }
            else {
                $this->core->set_raw_message($this->_message, false);
                $this->core->set_message_data(false, !$this->_status);
            }
        }
    }

    /*
    protected function render_before() {}
    */

    /**
     * Passed collection filter
     * after this method, $filter->apply called
     *
     * @param collection_filter
     */

    /*
    protected function render_after($filt = null) {}
    */

    protected function apply_grid_filters(collection_filter $col_filter) {
        if (!empty($this->grid_filters)) {

            $fields = $this->collection->fields();

            foreach ($fields as $key => $field) {
                $filter = @$field['filter'];
                if (isset($filter) && isset($this->grid_filters[$key])) {

                    if (!isset($filter['params'])) {
                        throw new collection_filter_exception('Empty params in filter : ' . $key);
                    }

                    $options = $filter['params'];
                    $col_filter->set_filter(
                        $key,
                        $this->grid_filters[$key],
                        $options[0],  // operator
                        @$options[1], // connector
                        @$options[2]  // raw
                    );
                }
            }

            foreach ($this->grid_filters as $key => $value) {
                // if ($this->params->offsetExists($key) && !$this->params->is_set($key)) {
                //    $filter->set_f $this->params->set($key, $value);
                // }
            }

        }

        if ($this->grid_sortables) {

            $this->collection->set_order();

            foreach ($this->grid_sortables as $f => $order) {
                if ($this->collection->field($f) && $order == 'ASC' || $order == 'DESC') {
                    $this->collection->order($f, $order);
                }
            }

        }
    }

    /**
     * Load data
     * Render list
     * Collection render
     *
     * Assign template variable: $return.list
     */
    function render() {

        if (!$this->with_model()) return;



        // if ($this->_limit) {

            $page = (int)$this->params->start;

            $filter = $this->_load(false)

                ->get_filter($this->base_url)
                ->with_clear(false)
                ->is_render_with_meta(true)

                //->set
                // todo set filters
                //->get_filters()
                //->set_config($this-)

                ->set_pagination($page, $this->_limit);

            $this->apply_grid_filters($filter);

            $this->render_before($filter);

            $result = $filter->apply();

            $this->render_after($result);

            // assign tpl
            $this->renderer->return->list = $result;

        /*
        } else {
            $this->_load();

            if (is_callable(array($this, 'render_after')))
                $this->render_after();

            $this->collection->render2edt();
        }
        */
    }

    /**
     * @param $status
     */
    function set_result($status) {
        $this->_status = $status;
    }

    /**
     * @param $msg
     * @param null $status
     * @return $this
     */
    function set_message($msg, $status = null) {
        $this->_message = $msg;
        if (isset($status)) $this->_status = $status;

        return $this;
    }

    /**
     * @param $redirect
     * @return $this
     */
    function set_redirect($redirect) {
        $this->_redirect = $redirect;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    function set_message_data($data) {
        $this->_message_data = $data;
        return $this;
    }

    function is_message_set() {
        return !empty($this->_message);
    }

    /**
     * Set ajax answer
     * @param null $status
     * @param null $message
     * @param null $data
     */
    function ajax_answer($status = null, $message = null, $data = null) {
        $this->context->core->ajax_answer($this->_get_response($status, $message, $data));
    }

    private $_ajax_answer_data;

    private function _get_response($status = null, $message = null, $data = null) {

        $response = array(
            'status'     => isset($status)  ? $status   : $this->_status
            , 'message'  => isset($message) ? $message  : $this->_message
            , 'data'     => isset($data)    ? $data     : $this->_message_data
        );

        if ($this->_redirect) {
            $response['redirect'] = $this->_redirect;
        }

        return $response;

    }

    /**
     * delayed ajax
     * call in action_xxx
     * for run action_after
     */

    protected function _ajax_answer($status, $message = null, $data = null) {
        $this->_ajax_answer_data = array(
              'status'   => isset($status) ? $status : $this->_status
            , 'message'  => isset($message) ? $message : $this->_message
            , 'data'     => $data
        );

        return $this;
    }

// actions //

    protected function action_flip_after() {
    }

    /** position */
    function action_flip() {

        $this->collection->flip_positions(
            functions::request_var('src', 0)
            , functions::request_var('dst', 0)
            , functions::request_var('ids', array(0))
            , functions::request_var('positions', array(0))
        );
        $this->action_flip_after();
        if ($this->in_ajax()) {
            $this->_ajax_answer(true, $this->i18n->T('Items position changed'));
        }

    }

    /*
    protected function action_drop_before($item) {}
    protected function action_drop_after()  {}
    */

    /**
     * Remove item
     */
    function action_drop() {

        $item = $this->_load_id();

        if (is_callable(array($this, 'action_drop_before')))
            $this->action_drop_before($item);

        $data   = $item->render();
        $status = $this->collection->remove($this->params->id);

        if (is_callable(array($this, 'action_drop_after')))
            $this->action_drop_after();

        if ($this->in_ajax()) {
            $this->_ajax_answer($status, $this->i18n->T('Item deleted'), $data);
        }
    }

    /**
     * Mass delete (selected IDs)
     */
    function action_drop_selected() {

        $ids = $this->request->post('ids');

        if (empty($ids)) {
            $this->_ajax_answer(false, $this->i18n->T('No items selected'));
        }

        $this->collection->clear()->set_where(
            'id IN (%s)', join(',', $ids)
        )->load();

        $count = $this->collection->count();

        $this->collection->remove_all();

        if (is_callable(array($this, 'action_drop_all_after')))
            $this->action_drop_all_after($ids);

        $this->_ajax_answer(true, $this->i18n->T('Items deleted') . ' (' . $count .  ')');
    }

    /*
    protected function action_drop_all_before() {}
    protected function action_drop_all_after()  {}
    */

    /**
     * Total clean up, danger
     */
    function action_drop_all() {
        $this->_load();

        if (is_callable(array($this, 'action_drop_all_before')))
            $this->action_drop_all_before();

        $count = $this->collection->count();
        $this->collection->remove_all();

        if (is_callable(array($this, 'action_drop_all_after')))
            $this->action_drop_all_after();

        if ($this->in_ajax()) {
            $this->_ajax_answer((bool)$count, $this->i18n->T('Items deleted'), $count);
        } else $this->set_message($this->i18n->T('Items deleted'));

        $this->disable_render(true);
    }


    protected function action_new_before() {}

    /**
     * New
     */
    function action_new() {
        $this->action_new_before();
        $this->collection->prepare2edt();
        $this->disable_render(true);

        // Generate sid for attachable collections
        if ($this->collection->config->get('attachable.master')) {
            $this->response->attach_sid = $this->collection->make_attach_sid();
        }
    }


    /** @return mixed ===false for action cancel */
    /*
    protected function action_modify_before(&$data, $item = false) {}
    protected function action_modify_after($id)     {}
    */

    function action_modify() {

        $item = $this->params->id ? $this->_load_id() : false;

        $result = true;

        if (is_callable(array($this, 'action_modify_before')))
            $result = $this->action_modify_before($this->postdata, $item);

        if ($result === false) {
            if (!$this->is_message_set()) $this->set_message($this->i18n->T('Action canceled'), false);
        } else {

            $id = $this->collection->modify($this->get_postdata(), $this->params->id);

            if (is_callable(array($this, 'action_modify_after')))
                $this->action_modify_after($id);

            if (!$this->is_message_set()) {

                $newbie = $this->collection->get_last_item();

                $data = $newbie ? array() : $newbie->render();

                if (core::is_debug()) {
                    $data['_sql'] = $this->collection->connection()->get_last_query();
                }

                // return to form
                if ($this->submit_type == 'apply') {
                    $this->set_redirect($this->get_edit_url($newbie));
                }

                // redirect `apply`
                //$data['redirect'] = $newbie->get_urls

                $this->set_message(
                    !$id ? $this->i18n->T('Action failed')
                         : $this->i18n->T($this->params->id ? 'Item modified' : 'Item added'), $id
                    )->set_message_data($data)
                    ;
            }
        }
    }

    /*
    protected function action_edit_before($item) {}
    protected function action_edit_after($item)  {}
    */

    function action_edit() {
        $item = $this->_load_id();

        if (is_callable(array($this, 'action_edit_before')))
            $this->action_edit_before($item);

        $this->collection->prepare2edt($this->params->id);

        if (is_callable(array($this, 'action_edit_after')))
            $this->action_edit_after($item);

        $this->disable_render(true);
    }

    /**
     * @todo secure code
     * change_field
     * quick_edit
     * POST: field: $this.attr('name'), value: $this.val()
     */
    function action_change_field() {

        $field = $this->request->post('field');
        $value = $this->request->post('value');

        if ($this->request->method == 'GET') {
            $this->_ajax_answer(false, $this->i18n->T('Method not available'));
            return;
        }

        if (!$field || !$this->collection->get_field($field)) {
            $this->_ajax_answer(false, $this->i18n->T('Field change failed'));
            return;
        }

        if (is_callable(array($this, 'action_change_field_before')))
            if (false === $this->action_change_field_before($field, $value)) return false;

        $_item = $this->_load_id();
        $_item->set_data($field, $value);
        $_item->update_fields($field);

        if ($this->in_ajax()) {
            $this->_ajax_answer(true, $this->i18n->T('Field modified'));
        }
        $this->disable_render(true);

        if (is_callable(array($this, 'action_change_field_after')))
            $this->action_change_field_after($field, $value);
    }

    /*
    protected function action_change_field_before($field, &$value) {}
    protected function action_change_field_after($field, $value) {}

    protected function action_active_before() {}
    protected function action_active_after()  {}
    */

    function action_active() {
        if (is_callable(array($this, 'action_active_before')))
            $this->action_active_before();

        $this->collection->toggle_active($this->params->id, ('true' == functions::request_var('to', 'false')));

        if (is_callable(array($this, 'action_active_after')))
            $this->action_active_after();

        if ($this->in_ajax()) {
            $this->_ajax_answer(true, $this->i18n->T('Status changed'));
        }
    }

    /**
     * Load item
     * @param null $id
     * @param bool $force
     * @return model_item
     * @throws controller_exception
     */
    protected function _load_id($id = null, $force = false) {
        $id = $id ? $id : $this->params->id;

        // loaded already
        if (!$force && ($return = $this->collection->get_item_by_id($id))) {
            return $return;
        }

        $return = $this->collection->load_only_id($id);

        if (!$return) {
            throw new controller_exception("_load_id({$id}) cant load item");
        }

        return $return;
    }

    /**
     * @param boolean load/or just return collection
     */
    protected function _load($load = true) {
        $this->collection->clear();

        if ($this->_where) $this->collection->set_where($this->_where);
        if ($this->_order) $this->collection->set_order($this->_order);

        return $load ? $this->collection->load() : $this->collection;
    }

    /**
     * public mixed __call ( string $name , array $arguments )
     */
    function __call($method, $params) {

        if (preg_match('@(before|after)$@', $method)) {
            core::dprint('Editor controller __called method: ' . $method);
            // allow empty events (parent::event())
            return;
        }

        throw new core_exception('Editor controller __called method: ' . $method);
    }

    /**
     * Query context (IOC)
     */
    function __get($key) {
        return $this->context->$key;
    }
}
