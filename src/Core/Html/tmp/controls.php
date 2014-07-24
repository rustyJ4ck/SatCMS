<?php
/**
 * @param $params
 * @param $template
 */
function smarty_function_control($params, $template) {

    $control = '';
    $name  = @$params['name'];
    $value = @$params['value'];
    $class = @$params['class'];
    $attrs = @$params['attrs'];

    // metadata (vf)
    $field = @$params['field'];
    $description = @$field['description'];

    if (!empty($description)) {
        $description = "<div class='help'>{$description}</div>";
    }

    $placeholder = @$params['placeholder'];

    if (!empty($placeholder)) {
        $placeholder = "placeholder='{$placeholder}'";
    }

    if (empty($name)) {
        throw new SmartyException('Empty control name ' . __FUNCTION__ . ' ' . var_export($params, 1));
    }

    switch (@$params['type']) {

// -----------------------------------------------------------------
        case 'date':
        case 'unixtime':
// -----------------------------------------------------------------

            $control = <<<CTRL
                <div class='input-group input-group-sm datetime' data-date-format="DD.MM.YYYY hh:mm">
                    <input type='text' class="form-control {$class}"
                        name="{$name}" {$attrs}
                        value="{$value}"
                        data-rule-datetime="true" {$placeholder}
                     />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
CTRL;

            break;

// -----------------------------------------------------------------
        case 'image':
// -----------------------------------------------------------------

            $previewTitle = $field['title']?:'Изображение';

            $preview = <<<CTLR
        <div class="inline-block">
        <a href="#" class="bootbox btn btn-info btn-sm"
            data-title="{$previewTitle}"
            data-content="<div class='bootbox-preview'><img src='{$value['url']}'/></div>">Показать</a>
        удалить <input type="checkbox" class="remove-image" value="remove" name="{$name}"/>
        </div>
CTLR;

/*
$control = <<<CTRL
     <i class='btn btn-primary'>
        <input type="file"
               style='opacity:0.01;position:absolute;margin-top:-8px;margin-left:-14px;height:36px'
               name="{$name}" size="40"
               onchange='console.log("file-click");$("#upload-file-info").html($(this).val());'
                   >

        <span id="upload-file-info">Chose file</span>
    </i>
CTRL;
            break;
*/
// -----------------------------------------------------------------
case 'file':
// -----------------------------------------------------------------

            if ($params['type'] == 'file') {

                $previewTitle = $field['title']?:'Файл';

                $preview = <<<CTLR
        <div class="inline-block">
        <a href="#" class="bootbox btn btn-info btn-sm"
            data-title="{$previewTitle}"
            data-content="<a href='{$value['url']}'>{$value['url']}</a>">Ссылка</a>
        удалить <input type="checkbox" class="remove-image" value="remove" name="{$name}"/>
        </div>
CTLR;

            }

            // var_dump($field);

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

            $control .= $description;

            break;


// -----------------------------------------------------------------
        case 'select':
// -----------------------------------------------------------------

            $default = @$params['default'];

            if (!isset($default)) {
                $default = array(0, 'Не указано');
            }

            $control = <<<CTRL
           <select name="{$name}" class="{$class}">
                <option value="{$default[0]}">{$default[1]}</option>
                %list%
            </select>
CTRL;

            $params['title'] = $params['title'] ?: 'title';

            if (!empty($params['src'])) {
                $options = array();
                foreach ($params['src'] as $id => $option) {
                    $options []= sprintf('<option value="%d" %s>%s</option>',
                        (@$option['id'] ?: $id),
                        ((!empty($params['value']) && $option['id'] == $params['value']) ? 'selected="selected"' : ''),
                        (is_array($option)?$option[$params['title']]:$option)
                    );
                }
                $options = join("\n", $options);

            } else {
                $options = '';
            }

            $control = str_replace('%list', $options,$control);



        default:
            break;

    }

    return $control;

}
