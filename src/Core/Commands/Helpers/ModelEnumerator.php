<?php

namespace SatCMS\Core\Commands\Helpers;

use Symfony\Component\Finder\Finder;
use loader, core;


class ModelEnumerator {

    static function find($module = '*') {

        $root   = loader::get_public() . loader::DIR_MODULES . $module . '/' . loader::DIR_MODELS;

        $finder = new Finder();
        $finder->directories()->in($root)->name('*')->depth('== 0');

        $models = array();

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {

            preg_match('@(?P<module>[\w_+]*)[\\\/]models[\\\/](?P<model>[\w_+]*)$@', $file, $matches);

            if (!empty($matches['module']) &&
               ('core' == $matches['module'] || core::modules()->is_registered($matches['module']))) {
                $model = $matches['module'] . '.' . $matches['model'];
                $models []= $model;
            }
        }

        return $models;
    }


}