<?php

/**
 * Multilang
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.9 2013/10/02 07:38:27 Vova Exp $
 */

namespace SatCMS\Core\Classes\Behaviors;

use core;

/**
 * Class Fields
 *
 * Model-item must implement get_extrafs_ids()
 */
class Multilang extends \model_behavior {

    /** @var \SatCMS\Core\Translation\I18n */
    private $i18n;

    private $fields;

    function configure() {
        $this->i18n = core::selfie()->i18n;

        $this->fields = array();

        foreach ($this->model->fields() as $id =>$field) {
            if (!empty($field['lang'])) {
                $this->fields []= $id;
            }
        }

    }

    /**
     * @return \model_collection
     */
    function locale_model() {
        return core::selfie()->model('locale');
    }

    function get_related() {

        return
        $this->locale_model()
            ->where('ctype_id', $this->model->get_ctype_id())
            ->where('pid', $this->model->id)
            ->where('lang', $this->i18n->lang)
            ->load();
    }

    function modify_after($data) {
        dd($this->fields, $data);
    }

    function prepare2edt_before() {

    }

    function render_after() {

    }

}