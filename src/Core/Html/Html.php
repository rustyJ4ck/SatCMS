<?php
/**
 * SatCMS  http://satcms.ru/
 * @author Golovkin Vladimir <rustyj4ck@gmail.com> http://www.skillz.ru
 */

namespace SatCMS\Core\Html;

/**
 * Class Html
 * @package SatCMS\Core\Html
 */
class Html {

    static $driver = 'Bootstrap';

    static function __callStatic($method, $params) {
        $control = static::create($method, $params[0]);
        return $control->render();
    }

    /**
     * @param $control
     * @param $params
     * @return HtmlElement
     */
    static function create($control, $params) {
        $control = ucfirst($control);
        $class = __NAMESPACE__ . '\\' . static::$driver . '\\' . $control . 'Control';
        return new $class ($params);
    }


}
