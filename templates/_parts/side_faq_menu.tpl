<div class="normal-frame">

<ul class="menus">
{foreach item=d from=$form.faq_category_list}
{assign "pagename" ""}
{if $d.faq_category_id == 1}
{assign "pagename" "service"}
{/if}
<li{if $base_url=="/help/$pagename"} class="active"{/if}><a href="/help/{$pagename}" title="{$d.title}">{$d.title}</a></li>
{/foreach}
</ul>

</div>