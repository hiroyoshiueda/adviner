<div id="main-content">

<div class="normal-frame">
<div class="htitle_with_bar bottom20"><h1>{$form.htitle}</h1></div>
<h2 class="htitle_with_line bottom5">相談窓口</h2>
<div class="base-list">
{if $form.advice_list}
{include file="_parts/advice_list.tpl" advice_list=$form.advice_list is_top=true}
{else}
<p class="dmsg">受付中の相談窓口はありません。</p>
{/if}
</div>
</div>

<div class="normal-frame">
<h2 class="htitle_with_line bottom5">相談したこと</h2>
<div class="base-list">
{foreach item=d from=$form.consult_list name="consult_list"}
<div class="list-item{if $smarty.foreach.consult_list.first} list-top{/if}">
	<div class="list-user">
		<span class="profile-frame-50">{profile_img user=$d size=50}</span>
	</div>
	<div class="list-info">
		<div class="list-status">{if $d.consult_status==1}<span class="label receiving">相談中</span>{else}<span class="label stop">相談終了</span>{/if}</div>
		<div class="list-message"><h3 class="list-htitle"><a href="/advice/{$d.advice_id}/{$d.consult_id}/" title="{$d.advice_title}">{$d.advice_title}</a></h3></div>
		<p>{$d.nickname}</p>
	</div>
	<div class="clear"></div>
</div>
{foreachelse}
<p class="dmsg">相談したことはありません。</p>
{/foreach}
</div>
</div>

{if $form.following_advice}
<div class="normal-frame">
<h2 class="htitle_with_line bottom5">フォローしている相談窓口</h2>
<div class="base-list">
{foreach item=d from=$form.following_advice name="following_advice"}
<div class="list-item{if $smarty.foreach.following_advice.first} list-top{/if}">
	<div class="list-user">
		<span class="profile-frame-50">{profile_img user=$d size=50}</span>
	</div>
	<div class="list-info">
		<div class="list-message"><h3 class="list-htitle"><a href="/advice/{$d.advice_id}/" title="{$d.advice_title}">{$d.advice_title}</a></h3></div>
		<p>{$d.nickname}</p>
	</div>
	<div class="clear"></div>
</div>
{/foreach}
</div>
</div>
{/if}

</div>

<div id="side-content">
{include file="_parts/side_user_view.tpl" user=$form.user user_rank=$form.user_rank}
{include file="_parts/ad/ad_side.tpl"}
</div>
