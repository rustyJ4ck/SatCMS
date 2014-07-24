<?php
/**
 * SatCMS  http://satcms.ru/
 * @author Golovkin Vladimir <rustyj4ck@gmail.com> http://www.skillz.ru
 */

namespace SatCMS\Core\Html;

abstract class HtmlElement extends \Registry {

    /*
    public $name;
    public $value;
    public $class;
    public $attrs;
    */

    protected $params;

    function __construct($params) {
        $this->params = $params;
        parent::__construct($params);
        $this->construct_after();
    }

    function construct_after() {
    }

    abstract function render();
}
