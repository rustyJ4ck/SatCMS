<?php

/**
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

namespace SatCMS\Core\Translation;

use core, loader;

class I18n {

    protected $lang;

    /** Языковые переменные */
    private $words = array();

    private $loaded = array();
    private $imports = array();

    function __construct() {
        $this->lang = core::selfie()->cfg('lang');
    }

    /**
     * Parse module words into one
     * huge array. Used in templates later.
     * Module lang start with m_
     * [lang.var]
     */
    public function import_words($module) {
        $this->imports []= $module;
    }

    protected function _import_words($module) {

        $lang_file = loader::get_public(loader::DIR_MODULES) . $module . '/' . loader::DIR_LANGS . $this->lang;

        if (file_exists($lang_file)) {

            $temp = parse_ini_file($lang_file, true);

            core::dprint('..i18n ' . $lang_file . " (x" . count($temp) . ")", core::E_DEBUG1);

            if ('core' == $module)
                $this->words = array_merge_recursive($this->words, $temp);
            else
                $this->words['_' . $module] = $temp;
        }

        $this->loaded []= $module;
    }

    /**
     * Load words only when needed
     */
    protected function load_words($module = null) {

        if (isset($module)) {

            if (!in_array($module, $this->loaded)) {
                $this->_import_words($module);
            }

            return;
        }

        if (count($this->loaded) != count($this->imports)) {
            $sync = array_diff($this->imports, $this->loaded);
            foreach ($sync as $m) {
                $this->_import_words($m);
            }
        }

    }

    /**
     * i18n
     * called from render
     * @todo refactor!
     */
    function get_words() {
        $this->load_words();
        return $this->words;
    }

    /**
     * i18n
     *
     * _T(...) raw text
     *
     * mod\section.string
     * mod.section.string
     *     section.string
     *
     * for translate, use module-based ::translate
     * @param string|array if array passed ['module', 'cont'], otherwise mod=core
     */
    function T($id, $params = null) {

        $mod      = false;
        $first_id = $id;

        // raw text _T(...)
        if (is_string($id) && preg_match('/^_T\((.*)\)$/', $id, $t)) {
            return $t[1];
        }

        $sid = false;

        if (is_array($id)) {
            $mod = $id[0];
            $sid = isset($id[2]) ? $id[1] : false;
            $id  = $sid ? $id[2] : $id[1];
        }

        if (($t = strpos($id, '\\')) || (substr_count($id, '.') >= 2 && ($t = strpos($id, '.')))) {
            $mod = substr($id, 0, $t);
            $id  = substr($id, $t + 1);
        }

        if ($t = strpos($id, '.')) {
            $sid = substr($id, 0, $t);
            $id  = substr($id, $t + 1);
            if ($sid == 'core' || array_key_exists('_' . $sid, $this->words)) {
                $mod = $sid;
                $sid = null;
            }
        }

        // core::dprint_r([$first_id, $mod,$sid, $id]);

        $this->load_words($mod?:'core');

        $return = ($mod && $mod != 'core')
            ? ($sid ? @$this->words['_' . $mod][$sid][$id] : @$this->words['_' . $mod][$id])
            : ($sid ? @$this->words[$sid][$id] : @$this->words[$id]);


        if (!$return) {
            core::dprint(
                array('[translate] %s, undefined : %s :: %s :: %s',
                    (is_array($first_id) ? 'array' : print_r($first_id, 1)), $mod, $sid, $id
                ), core::E_NOTICE);
            $return = $id;
        }

        return $return;
    }
}