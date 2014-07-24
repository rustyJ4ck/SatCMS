{block 'content'}


    <div class="box">


        <div class="box-content padded">

            <div class="pull-right" style="margin:0;">

            </div>

            <div class="btn-group btn-group-lg">

                {foreach $controller.types as $type}

                    <a  class="btn btn-default"
                        href="type_id/{$type.id}/">{$type.title}</a>

                {/foreach}

            </div>

        </div>
    </div>

    {*include "widgets/model/list.tpl"*}


{/block}