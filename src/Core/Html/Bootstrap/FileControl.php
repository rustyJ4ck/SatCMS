<?php
/**
 * SatCMS  http://satcms.ru/
 * @author Golovkin Vladimir <rustyj4ck@gmail.com> http://www.skillz.ru
 */

namespace SatCMS\Core\Html\Bootstrap;

use SatCMS\Core\Html\HtmlElement;

class FileControl extends HtmlElement {

    function construct_after() {
        $this->type = 'file';
    }

    function render() {

        $name = $this->name;
        $value = $this->value;

        if ($this->type == 'file') {

            $previewTitle = $this->title ?: 'Файл';

            if (!empty($value['url'])) {
                $preview = <<<CTLR
                    <div class="inline-block">
                    <a href="#" class="bootbox btn btn-info btn-sm"
                        data-title="{$this->previewTitle}"
                        data-content="<a href='{$value['url']}'>{$value['url']}</a>">Ссылка</a>
                    удалить <input type="checkbox" class="remove-image" value="remove" name="{$name}"/>
                    </div>
CTLR;
            }

        }

        $control = <<<CTRL
             <div class="inline-block">
             <i class='btn btn-default btn-sm btn-file'>

                <input type="file"
                       class="masked"
                       name="{$name}" size="40"
                       onchange='$(this).next().html($(this).val());'
                       />

                <span>Загрузить файл</span>
            </i>
            </div>

CTRL;

        if (!empty($value['url'])) {
            $control .= $preview;
        }

        $control .= $this->description;

        /*
        $control = htmlspecialchars($control);
        $control .= print_r($this->as_array(),1);
        */

        return $control;

    }

}