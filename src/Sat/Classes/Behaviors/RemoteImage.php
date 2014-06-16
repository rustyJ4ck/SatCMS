<?php

/**
 * Allow remote image upload
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.9 2013/10/02 07:38:27 Vova Exp $
 */

namespace SatCMS\Sat\Classes\Behaviors;

use core;
use loader;

class RemoteImage extends \model_behavior {

    function get_remote_keys() {

        $filtered = array();

        $fields = $this->model->fields();

        foreach ($fields as $key => $field) {
            if ($field['type'] == 'image' && @$field['remote']) {
                $filtered []= $key;
            }
        }

        return $filtered;
    }

    /**
     * @param $data
     */
    function modify_before($data) {

        $remotes = $this->get_remote_keys(); // array('image', 'alt_image');

        foreach ($remotes as $_key) {

            $key = $_key . '_url';

            // load image from url
            if (!empty($data[$key])) {

                $ext = substr($data[$key], strrpos($data[$key], '.') + 1);

                // @fixme types from model
                if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {

                    $buffer = null;

                    /** @var http $http */
                    $http = core::lib('http');

                    try {
                        $buffer = $http->fetch($data[$key]);
                    }
                    catch (\http_exception $e) {
                        core::dprint('Upload failed...');
                    }

                    if ($buffer) {
                        $tmpname = tempnam(loader::get_temp(), time()) . '.' . $ext;
                        file_put_contents($tmpname, $buffer);

                        $data[$_key] = array(
                            'tmp_name' => $tmpname
                            , 'name'     => basename($data[$key])
                            , 'size'     => filesize($tmpname)
                        );

                    }

                }
                else {
                    core::dprint(__METHOD__ . ': file url skipped, extension deny');
                }
            }

        }

    }

}