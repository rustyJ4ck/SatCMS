{*$config.base_url}"*}

{extends 'widgets/model/grid.tpl'}

{block 'params' append}

    {$params="new: yes, edit: yes, remove: yes, filterTitle: 'Текстовые блоки для использования в шаблнах'"}
    {*$model     = $return.list.data*}
    {$btnNew="title: 'Добавить снипет', dialog: yes, dialogTitle: 'Новый снипет'"}
    {$btnEdit="dialogTitle: 'Правка снипета', dialog: yes"}

    {$actionUrlExtra = "pid=`$req.pid`"}

{/block}

{*description="Блоки кэшируются! При правке текстов изменения на сайте будут видны с задержкой."*}
