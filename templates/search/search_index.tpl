<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-last" href="{$base_url}">{$form.htitle}</a>
</div>

<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom15"><h1>{$form.htitle}</h1></div>

{include file="_parts/search_form.tpl" prefix=""}

<div class="bottom15">
{pager2 total=$form.list_total limit=$form.list_limit prev_format="前のページ" next_format="次のページ" url=$form.pagerf show_disable="1"}
</div>

<div id="advice-list" class="base-list">
{if $form.list}
{include file="_parts/advice_list.tpl" advice_list=$form.list is_top=true}
{else}
	{if $form.q!=""}
<p id="search-msg" class="dmsg"><strong>{$form.q}</strong> に関する相談窓口は見つかりませんでした。</p>
	{else if $form.t!=""}
<p id="search-msg" class="dmsg">タグ「<strong>{$form.t}</strong>」を含む相談窓口は見つかりませんでした。</p>
	{/if}
{/if}
</div>

<div class="bottom20" style="margin-top:15px;">
{pager2 total=$form.list_total limit=$form.list_limit prev_format="前のページ" next_format="次のページ" url=$form.pagerf show_disable="1"}
</div>

</div>
</div>

<div id="side-content">

{include file="_parts/ad/ad_side.tpl"}
{include file="_parts/side_feedback_form.tpl"}
{include file="_parts/ad/ad_side_text.tpl"}

</div>
