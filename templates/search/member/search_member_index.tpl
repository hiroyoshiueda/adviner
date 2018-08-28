<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-last" href="{$base_url}">{$form.htitle}</a>
</div>

<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom15"><h1>{$form.htitle}</h1></div>

<div class="bottom15">
{pager2 total=$form.list_total limit=$form.list_limit prev_format="前のページ" next_format="次のページ" url=$form.pagerf show_disable="1"}
</div>

<div class="base-list">
{if $form.list}
{foreach item=d from=$form.list name="member_list"}
<div class="list-item">
<div class="list-user">
	<span class="profile-frame-50">{profile_img user=$d size=50}</span>
</div>
<div class="list-info">
	<div class="list-message"><h2 class="list-title">{$d|username nofilter}</h2></div>
	<div class="list-message"><p>{$d.profile_msg|list_message:100 nofilter}</p></div>
</div>
<div class="clear"></div>
</div>
{/foreach}
{else if $form.q != ""}
<p id="search-msg" class="dmsg"><strong>{$form.q}</strong> を含むメンバーは見つかりませんでした。</p>
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
