{foreach from=$parameters item=d}
{if $d.value|@gettype == "array"}
{foreach item=v from=$d.value name="_hidden_array"}
<input type="hidden"{if $show_id}  id="_{$d.key}{$smarty.foreach._hidden_array.index}"{/if} name="{$d.key}[]" value="{$v}" />
{/foreach}
{else}
<input type="hidden"{if $show_id} id="_{$d.key}"{/if} name="{$d.key}" value="{$d.value}" />
{/if}
{/foreach}