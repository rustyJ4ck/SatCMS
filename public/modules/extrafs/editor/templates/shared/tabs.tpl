{* <li><a href="#t-extra" data-toggle="tab"><span>Контент</span></a></li> *}

{if count($data)}
{foreach $data as $tab}
<li><a href="#t-{$tab.name}" data-toggle="tab"><span>{$tab.title}</span></a></li>
{/foreach}
{/if}