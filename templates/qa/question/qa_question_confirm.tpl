<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-last" href="{$base_url}" title="{$form.htitle}">{$form.htitle}</a>
</div>

<div id="main-content">

<div class="normal-frame">

<div class="htitle_with_bar bottom15">
<h1>{$form.htitle}</h1>
</div>

<div class="base-list bottom10">
{include file="_parts/alert_errormsg.tpl"}
<form id="mainform" method="post" action="finished">
{include file="_parts/hidden.tpl" show_id=true}

	<div class="infomsg">
	<strong>まだ送信されていません。</strong>入力内容を確認し [投稿する] ボタンを押してください。
	</div>

	<div class="list-user">
		<span class="profile-frame-50">{profile_img user=$userInfo size=50}</span>
	</div>
	<div class="list-info">
		<div class="input_item">
			<label>質問タイトル</label>
			<p>{$form.question_title}</p>
		</div>
		<div class="input_item">
			<label>質問内容</label>
			<p>{$form.question_body|makebody nofilter}</p>
		</div>
		<div class="input_item">
			<label>カテゴリー</label>
			{if $form.category_id>0}
			<p>{$form.category_set[$form.category_id].cname}</p>
			{else}
			{*<p>（選択しない）</p>*}
			{/if}
		</div>
{*		<div class="input_item">
			<label>回答者</label>
			<p>{if $form.limit_type==1}アドバイザーのみ{else}誰でも{/if}</p>
		</div>*}

<div class="confirm-btn-frame bottom10">
<div style="text-align:right;"><a onclick="$('#backform').submit();">&laquo; 入力した内容を修正する</a></div>
<ul class="btn_area">
	<li><a class="medium-btn green-btn" id="mainform-btn" onclick="return false;">{if $form.id>0}変更する{else}投稿する{/if}</a></li>
	<li><span id="send-loader" class="hide-loader"></span></li>
</ul>
<div class="clear"></div>
</div>

	</div>
<div class="clear"></div>
</form>

<form id="backform" method="post" action="form">
{include file="_parts/hidden.tpl"}
</form>

</div>

</div>
</div>

<div id="side-content">
{include file="_parts/side_user_view.tpl" user=$form.user user_rank=$form.user_rank}
{include file="_parts/ad/ad_side.tpl"}
</div>

<script>
var sendButton = function()
{
	$('#mainform-btn').unbind('click', sendButton).addClass('green-btn-disabled');
	$('#send-loader').css('display', 'inline-block');
	$('#mainform').submit();
}
$(function(){
	$('#mainform-btn').bind('click', sendButton);
});
</script>
