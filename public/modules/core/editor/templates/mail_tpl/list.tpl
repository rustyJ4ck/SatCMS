{*$config.base_url}"*}

{include "widgets/model/filter.tpl"
    params="new: yes"
    btnNew="dialog: yes"
    model=$return.list.collection
}

{include "widgets/model/list.tpl"
    btnEdit="dialog: yes"
    model=$return.list.collection
}
