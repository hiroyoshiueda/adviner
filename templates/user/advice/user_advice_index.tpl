<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom15">
<h1>{$form.htitle}</h1>
</div>

{include file="_parts/alert_successmsg.tpl"}

<div class="base-list bottom10">
{foreach item=d from=$form.advice_list name="advice_list"}
<div class="list-item {if $smarty.foreach.advice_list.first} list-top{/if}"{if $d.advice_status == 3} style="background-color:#fff0f0"{/if}>
<div class="list-status">
{if $d.public_type == 1}
{assign 'icon_private' '<img src="/img/icon_private.png" width="16" height="16" class="icon-top" alt="非公開" />'}
{else}
{assign 'icon_private' ''}
{/if}
{if $d.charge_flag == 1}
{assign 'icon_charge' '<img src="/img/icon_charge.png" width="16" height="16" class="icon-top" alt="有料相談" />'}
{else}
{assign 'icon_charge' ''}
{/if}
{if $d.advice_status == 3}
	<span class="label refuse right3">再申込み</span>{$icon_private nofilter}{$icon_charge nofilter}（<span style="color:#f00;">承認されませんでした。入力内容を確認の上、再度登録申込みを行ってください</span>）
{else if $d.advice_status == 2}
	<span class="label examine right3">承認待ち</span>{$icon_private nofilter}{$icon_charge nofilter}（<span style="color:#999;">承認後に公開されます</span>）
{else if $d.advice_status == 1}
	<span class="label receiving right3">受付中</span>{$icon_private nofilter}{$icon_charge nofilter}（<a href="#" onclick="changeAdviceStatus({$d.advice_id},0);return false;">相談窓口を一時停止する</a>）
{else}
	<span class="label stop right3">停止中</span>{$icon_private nofilter}{$icon_charge nofilter}（<a href="#" onclick="changeAdviceStatus({$d.advice_id},1);return false;">相談窓口を再開する</a>）
{/if}
</div>
<div class="list-message" style="margin-bottom:6px;">
	<p><a href="/advice/{$d.advice_id}/">{$d.advice_title}</a></p>
</div>
{if $d.advice_status != 2}
<div class="left_area">
	{if $d.advice_status == 1}
	{$var_pagetitle="[相談窓口]`$d.advice_title` - `$userInfo.nickname``$smarty.const.APP_CONST_SITE_TITLE4`"}
	<ul class="social-top">
		<li>{"`$REAL_URL`advice/`$d.advice_id`/"|social_fb_share nofilter}</li>
		<li>{"`$REAL_URL`advice/`$d.advice_id`/"|social_twitter:$var_pagetitle:"horizontal" nofilter}</li>
	</ul>
	<div class="clear"></div>
	{/if}
</div>
<div class="right_area">
	<ul class="horizontal" style="margin-top:3px;font-size:93%;">
		<li style="margin-right:10px;"><span class="img-pen right5"></span><a href="/user/advice/contact/edit?id={$d.advice_id}" class="advice-tool-edit" title="編集する">編集する</a></li>
		<li><span class="img-delete right5"></span><a href="#" onclick="deleteAdvice({$d.advice_id});return false;" class="advice-tool-delete" title="削除する">削除する</a></li>
	</ul>
	<div class="clear"></div>
</div>
<div class="clear"></div>
{/if}
</div>
{foreachelse}
<div class="infomsg" style="margin-top:8px;">
<strong>あなたは相談を受付けていません。</strong>
<p>どんなことでも、きっと誰かの役に立つはず。まずは <a href="/user/advice/contact/">相談窓口</a> を登録してみよう！</p>
</div>
{/foreach}
</div>

</div>
</div>

<div id="side-content">

{include file="_parts/side_mypage_menu.tpl"}

{include file="_parts/side_feedback_form.tpl"}

</div>

<script>
function changeAdviceStatus(id, status)
{
	var msg = status==1 ? '相談窓口を再開しますか？' : '相談窓口を停止しますか？\n※新規の相談が停止されるだけで過去の相談スレッドは公開されます。';
	if (confirm(msg))
	{
		window.location.href='/user/advice/contact/change_status?id='+id+'&advice_status='+status;
	}
}
function deleteAdvice(id)
{
	if (confirm('相談窓口を削除しますか？\n※削除した相談窓口は復元できません。一時的な停止は「相談窓口を一時停止する」を選んでください。'))
	{
		window.location.href='/user/advice/contact/delete?id='+id;
	}
}
</script>