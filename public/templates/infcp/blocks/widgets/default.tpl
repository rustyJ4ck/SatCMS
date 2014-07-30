<div class="{$block.data.class}">
{foreach $block.data.widgets as $item}
    {if $item.active}
    <div class="panel panel-default {$item.class}">
        <div class="panel-heading">
        {$item.title}
        </div>
        <div class="panel-body ">
        {$item.content}
        </div>
    </div>
    {/if}
{/foreach}
</div>

{*  

template => "widgets/default"
title => "Виджеты"
params => Array (1)
  name => "sidebar_right"
data => Array (7)
  id => 3
  title => "Правый блок"
  name => "sidebar_right"
  class => ""
  text => ""
  site_id => 2
  widgets => Array (3)
    0 => Array (9)
      id => 1
      text => " @hello@"
      title => "Блок 1"
      class => ""
      raw => false
      active => true
      plain => false
      pid => 3
      position => "1"

*}