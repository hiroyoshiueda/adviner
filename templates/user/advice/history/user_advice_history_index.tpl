<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom15">
<h1>{$form.htitle}</h1>
</div>

{include file="_parts/alert_successmsg.tpl"}

{pager total=$form.consult_total limit=$form.consult_limit prev_format="前のページ" next_format="次のページ" show_disable="1"}

<div class="base-list bottom10">
{foreach item=d from=$form.consult_list name="consult_list"}
<div class="list-item{if $smarty.foreach.consult_list.first} list-top{/if}"{if $d.review_state == 0 && $d.consult_status == 1} style="background-color:#ffc;"{/if}>
<div class="list-user">
	<span class="profile-frame-50">{profile_img user=$d size=50}</span>
</div>
<div class="list-info">
	<div class="list-status">
		{if $d.consult_status==1}<span class="label running right3">相談中</span>{else}<span class="label stop right3">相談終了</span>{/if}
		{if $d.public_flag == 1}<img src="/img/icon_private.png" width="16" height="16" class="icon-top" alt="非公開" />{/if}
		{if $d.advice_charge_flag == 1}<img src="/img/icon_charge.png" width="16" height="16" class="icon-top" alt="有料相談" />{/if}
		{if $d.review_state == 0 && $d.consult_status == 1}<span style="color:#ff9900;">（未回答）</span>{/if}
	</div>
	<div class="list-message">
		<p><a href="/advice/{$d.advice_id}/{$d.consult_id}/">{$d.consult_body|list_message:100 nofilter}</a></p>
	</div>
	<div class="list-message">
		<p>{$d.nickname}</p>
	</div>
	<div class="list-message list-date">
		<p>相談日：{$d.createdate|date_zen_f}　相談期限：{if $d.advice_charge_flag==1}なし{else}{$d.finishdate|date_zen_f}{/if}</p>
	</div>
	<div class="list-message list-off">
		<a href="/advice/{$d.advice_id}/">&raquo; {$d.advice_title}</a>
	</div>
</div>
<div class="clear"></div>
</div>
{foreachelse}
<div class="infomsg">
<strong>まだ相談されたことはありません。</strong>
</div>
{/foreach}
</div>

{pager total=$form.consult_total limit=$form.consult_limit prev_format="前のページ" next_format="次のページ" show_disable="1"}

</div>
</div>

<div id="side-content">

{include file="_parts/side_mypage_menu.tpl"}

{include file="_parts/side_feedback_form.tpl"}

</div>