{if $tpl_parent_chain}

    <div class="padded-bottom padded-hor box-breadcrumb">

            {*<a href="{$config.base_url}">Корень</a>:*}
            <ul class="breadcrumb">
            {foreach $tpl_parent_chain.data as $item}

                {* if NOT $item@first}&raquo;{/if} <a href="{$item.urls.editor_view}">{$item.title}</a>*}

                {if $item.c_children}
                <li class="dropdown">

                <a href="{$item.urls.editor_view}" class="dropdown-toggle" data-toggle="dropdown">
                    {$item.title}
                    <b class="caret"></b>
                </a>

                    <ul class="dropdown-menu">

                        <li >
                            <a>
                                <span href="/editor/sat/node/id/{$item.id}/op/edit/"
                                      class="clickable btn btn-xs btn-default {if $item.id == $currentID}btn-success{/if}">
                                      <i class="glyphicon glyphicon-pencil"></i></span>

                                <span class="clickable btn btn-xs {if $item.id == $currentID}btn-success{/if}"
                                      href="/editor/sat/node/pid/{$item.id}/">
                                      {$item.title}
                                      </span>
                            </a>
                        </li>


                    {* @fixme page count > 30 break dropdown  *}

                    {$i = 0}
                    {foreach $current.site.tree as $subitem}
                        {if $item.id == $subitem.pid && (!$model.id || $subitem.id >= $model.id)}
                        {if  $i++ > 25}{break}{/if}
                        <li>
                            <a>

                                <span href="/editor/sat/node/id/{$subitem.id}/op/edit/"
                                      class="clickable btn btn-xs btn-info">
                                      <i class="glyphicon glyphicon-pencil"></i></span>

                                <span class="clickable btn btn-xs {if $subitem.id == $currentID}btn-success{/if}"
                                      href="/editor/sat/node/pid/{$subitem.id}/">
                                      {$subitem.title}
                                      </span>

                            </a>
                        </li>
                        {/if}
                    {/foreach}
                    </ul>
                </li>
                {else}

                    <li><a href="{$item.urls.editor_view}">
                        {$item.title}
                    </a>
                    </li>
                {/if}


            {/foreach}
            </ul>

    </div>

{/if}