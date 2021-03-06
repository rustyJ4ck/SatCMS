<?php


/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: module_orm.php,v 1.4.2.2.2.2 2013/01/30 06:53:20 Vova Exp $
 */

/**
 * Models factory
 *
 * model:
 *   fields = {
 *   }
 *   config = {
 *       'table' => '%class%' //use model name module_model
 *   }
 *
 */
abstract class module_orm {

    private $_models;

    /**
     * Register model instance
     *
     * module name prefixed with {@see loader::CLASS_PREFIX}
     *
     * @param string  module_name
     * @param array   config ( order_sql , where_sql, limit_sql )
     *                tpl_table - template name block
     *                extend - extend base model classes, chrooted in /model/{extend}/*.php, naming: {extend}_base_collection
     * @param boolean standalone (регистрировать в системе или нет)
     *
     * @return model_collection
     *
     * @throws core_exception
     */
    function model($model, $config = array(), $standalone = true) {

        // check for module.model
        $module = '';

        if (is_string($model) && false !== strpos($model, '.')) {
            $model = explode('.', $model);
        }
        if (is_array($model)) {
            $module = $model[0];
            $model  = $model[1];
        }

        // call external module
        if (!empty($module) && $module != $this->get_name()) {
            return core::module($module)->model($model, $config, $standalone);
        }

        $_model = $model;

        $is_extended = false;

        if (isset($config['extend'])) {
            $_model      = $config['extend'] . '_' . $_model;
            $is_extended = true;
        }

        // return if not standalone & registered
        if (!$standalone && $this->_registered($model)) return $this->_models[$model];

        $model_prefix = '';

        /*
         * `with_module_prefix` turn on by default on all modules, rather than `users` and `core`
         * /class/{model}/collectoin.php --> {module}_{model}_collection
         */

        $this_module = $this->get_name();

        if ($this_module != 'core' && $this_module != 'users') {
            $config['with_module_prefix'] = true;
        }

        if (isset($config['with_module_prefix'])) {
            $model_prefix = $this->get_name() . '_';
        }

        /*   check modules path
             if yes, make namespace for it
        */

        $path_prefix = $this->root_dir;

        /* standalone class support
           without register in $core->classes db

           class naming:
           /classes/model/
                 +- collection (model_collection)
                 +- item (model_item)
        */

        $m_class      = $model_prefix . $model . '_collection';
        $m_class_item = $model_prefix . $model . '_item';

        // chroot support
        // [tf]obsolete: auto chroot to module
        $f_class_path = $path_prefix . 'models/' . $model . '/';

        $f_class = array(
            $f_class_path . 'collection' . loader::DOT_PHP,
            $f_class_path . 'item' . loader::DOT_PHP
        );

        if (!class_exists($m_class, 0)) {
            if (!file_exists($f_class[0])) {
                core::dprint(__METHOD__ . " '{$m_class}' ({$model}) fallback to model_collection", core::E_DEBUG4);
            } else {
                require $f_class[0];
            }
        }

        if (!class_exists($m_class_item, 0) && file_exists($f_class[1])) {
            require $f_class[1];
        }

        if (!$standalone && isset($this->_models[$model])) {
            return $this->_models[$model];
        }

        // new one
        $new_config = array(
             'table' => ($model_prefix . $model)
           , 'root'    => $f_class_path
        );

        // fallback
        if (!class_exists($m_class, 0)) {
            $new_config['class'] = $m_class;
            $m_class = 'model_collection';
        }

        if (!class_exists($m_class_item, 0)) {
            $m_class_item  = 'model_item';
            $new_config['item_class'] = $m_class_item;
        }

        if (!isset($config['tpl_table'])) {
            $config['tpl_table'] = $model_prefix . $model;
        }

        if (is_array($config)) $new_config = array_merge($new_config, $config);

        if (!class_exists($m_class, 0)) {
            throw new core_exception('Cant register collection, no class : ' . $m_class);
        }

        $tmp = new $m_class($new_config);

        if ($standalone) return $tmp;

        $this->_models[$model] = $tmp;

        return $this->_models[$model];
    }

    /**
     * Check for registered class
     */
    private function _registered($id) {
        return array_key_exists($id, $this->_models);
    }

    /**
     * Destroy class
     */
    private function _destroy($id) {
        if ($this->_registered($id)) {
            unset($this->_models[$id]);
        }
    }

}


