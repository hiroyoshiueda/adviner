<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom15">
<h1>{$form.htitle}</h1>
</div>

{include file="_parts/alert_successmsg.tpl"}

{pager total=$form.advice_total limit=$form.advice_limit prev_format="前のページ" next_format="次のページ" show_disable="1"}

<div id="friends-advice-list" class="base-list bottom15">
{if $form.advice_list}
{foreach item=d from=$form.advice_list name="advice_list"}
<div class="list-item{if $smarty.foreach.advice_list.first} list-top{/if}">
	<div class="list-user">
		<span class="profile-frame-50">{profile_img user=$d size=50}</span>
	</div>
	<div class="list-info">
		<p>{$d.nickname}</p>
		<div class="list-message">
			<a href="/advice/{$d.advice_id}/" title="{$d.advice_title}">{$d.advice_title}</a>
		</div>
		<div class="list-message">
			{"1"|follow_btn:$d.advice_id:"a":$d.advice_title nofilter}
		</div>
	</div>
	<div class="clear"></div>
</div>
{/foreach}
{else}
<div><p>まだフォローした相談窓口はありません。</p></div>
{/if}
</div>

{pager total=$form.advice_total limit=$form.advice_limit prev_format="前のページ" next_format="次のページ" show_disable="1"}

</div>
</div>

<div id="side-content">

{include file="_parts/side_mypage_menu.tpl"}

{include file="_parts/side_feedback_form.tpl"}

</div>