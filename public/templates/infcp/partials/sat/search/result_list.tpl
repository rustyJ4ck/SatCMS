{*$return|@debug_print_var*}
<table class="nice_borders" style="width:100%;">
<tr>
<th>Заголовок</th>
<th>Описание</th>
<th>Добавлен</th>
</tr>
{foreach from=$return.posts.data item=i}
<tr>
<td>
<a href="{$i.url}" rel="({$i.id})">{$i.title|escape}</a>
</td>
<td class="help">
{$i.description}&nbsp;
</td>
<td>
{if $i.time}{$i.time}{else}&nbsp;{/if}
</td>
{/foreach}
</table>

