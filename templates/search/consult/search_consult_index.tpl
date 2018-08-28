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
{foreach item=d from=$form.list name="consult_list"}
<div class="list-item{if $smarty.foreach.consult_list.first} list-top{/if}">
<div class="list-fromto-user">
	<ul class="list-fromto">
		<li><span class="profile-frame-32">{profile_img user=$form.user_set[$d.consult_user_id] size=32}</span></li>
		<li class="list-fromto-arrow"></li>
		<li><span class="profile-frame-32">{profile_img user=$form.user_set[$d.advice_user_id] size=32}</span></li>
	</ul>
</div>
<div class="list-fromto-info">
	<div class="list-message list-textlink"><a href="/advice/{$d.advice_id}/{$d.consult_id}/">{$d.reply_body|list_message:200 nofilter}</a></div>
	<div class="list-message dmsg"><a href="/advice/{$d.advice_id}/" title="{$d.advice_title}">{$d.advice_title}</a></div>
</div>
<div class="clear"></div>
</div>
{/foreach}
{else if $form.q!=""}
<p id="search-msg" class="dmsg"><strong>{$form.q}</strong> に関する相談・アドバイスは見つかりませんでした。</p>
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
