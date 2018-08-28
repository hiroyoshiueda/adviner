{if $gnavi_titles}
<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
{foreach from=$gnavi_titles item=d name="gnavi_list"}
{if !$smarty.foreach.gnavi_list.last}
<a class="gnavi-link" href="{$gnavi_links[$smarty.foreach.gnavi_list.index]}">{$d}</a>&gt;
{else}
<a class="gnavi-last" href="{$gnavi_links[$smarty.foreach.gnavi_list.index]}">{$d}</a>
{/if}
{/foreach}
</div>
{/if}