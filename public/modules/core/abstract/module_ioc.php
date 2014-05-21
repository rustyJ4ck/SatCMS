<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: module_ioc.php,v 1.8.2.4 2013/05/15 07:19:31 Vova Exp $
 */

class module_ioc {

    public $dependencies = array();
    private $context;

    function __construct($dependencies = null, $context) {

        if (empty($dependencies)) {
            return;
        }

        $this->context = $context;

        $this->dependencies = (is_array($this->dependencies))
            ? functions::array_merge_recursive_distinct($dependencies, $this->dependencies)
            : $dependencies;
    }

    /**
     * @param $name
     * @return mixed
     * @throws core_exception
     */
    public function resolve_dependency($name) {

        core::dprint(array('..ioc-resolve %s %s', $name, (isset($this->dependencies[$name]['instance'])?'+':'-')));

        if (isset($this->dependencies[$name]) && !isset($this->dependencies[$name]['instance'])) {

            $class = $this->dependencies[$name]['class'];

            $this->dependencies[$name]['instance'] = false;

            if (!empty($this->dependencies[$name]['require'])) {
                if (!file_exists($this->dependencies[$name]['require'])) {
                    core::dprint('IOC: require failed ' . $this->dependencies[$name]['require']);
                } else {
                    require_once $this->dependencies[$name]['require'];
                }
            }

            core::dprint(array('..ioc-resolve %s, %s, %s', $name, (is_string($class)?$class:'closure'), @$this->dependencies[$name]['require']), core::E_DEBUG2);

            if (!empty($class)) {

                if ($class instanceof Closure) {

                    $this->dependencies[$name]['instance'] = $class();

                }
                elseif (class_exists($class)) {

                     if (!empty($this->dependencies[$name]['params'])) {

                         $params = $this->dependencies[$name]['params'];

                         if ($params instanceof Closure) {
                             $params = $params($this->context);
                         }

                         if (count($params) == 1) {
                             $this->dependencies[$name]['instance'] = new $class(array_shift($params));
                         } else {
                             $reflection_class = new ReflectionClass($class);
                             $this->dependencies[$name]['instance'] = $reflection_class->newInstanceArgs($params);
                         }

                     } else {
                         $this->dependencies[$name]['instance'] = new $class($this->context);
                     }

                }
            }

            /**
             * If no class, alias to fallback
             */
            if (!$this->dependencies[$name]['instance']
                && !empty($this->dependencies[$name]['fallback'])
            ) {

                $fallback = $this->dependencies[$name]['fallback'];

                if (is_callable($fallback)) {
                    $this->dependencies[$name]['instance'] = $fallback ($class);
                } else {
                    core::dprint(array('IOC fallback [%s] %s -> %s', $name, $fallback, $class), core::E_DEBUG4);

                    if (empty($class)) {
                        $class = $fallback;
                    } else {
                        class_alias($fallback, $class);
                    }

                    $this->dependencies[$name]['instance'] = new $class ($this->context);

                }

            }

            if (!$this->dependencies[$name]['instance']  && !empty($this->dependencies[$name]['required'])) {
                throw new core_exception('IOC cant resolve: ' . $name);
            }

        }



        return $this->dependencies[$name]['instance'];
    }

}