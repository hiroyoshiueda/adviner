<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-last" href="{$base_url}" title="{$form.htitle}">{$form.htitle}</a>
</div>

<div id="main-content">

<div class="normal-frame">

{if $userInfo.id>0}

<div class="htitle_with_bar bottom15">
<h1>{$form.htitle}</h1>
</div>

{include file="_parts/alert_errormsg.tpl"}

<div class="bottom10">
<span class="required">※</span>&nbsp;必須項目
</div>

<div class="base-list bottom10">
<form id="mainform" method="post" action="confirm">
{include file="_parts/hidden.tpl" show_id=true}
	<div class="list-user">
		<span class="profile-frame-50">{profile_img user=$userInfo size=50}</span>
	</div>
	<div class="list-info">
		<div class="input_item">
			<label>質問タイトル&nbsp;<span class="required">※</span></label>
			<input id="question_title" name="question_title" value="{$form.question_title}" class="w490 ime-on" size="20" type="text" />
			{$form.errors.question_title|errormsg nofilter}
		</div>
		<div class="input_item">
			<label>質問内容&nbsp;<span class="required">※</span></label>
			<textarea id="question_body" name="question_body" class="w490 ime-on" cols="20" rows="10">{$form.question_body}</textarea>
			{$form.errors.question_body|errormsg nofilter}
		</div>
		<div class="input_item">
			<label>カテゴリー&nbsp;<span class="required">※</span></label>
			{tag type="select-group" name="category_id" options=$categoryOptions groups=$AppConst.mainCategorys selected=$form.category_id blank="（未選択）"}
			{$form.errors.category_id|errormsg nofilter}
		</div>
{*		<div class="input_item">
			<label>回答者</label>
			{tag type="radio" name="limit_type" value="0" checked=$form.limit_type label="誰でも"}
			{tag type="radio" name="limit_type" value="1" checked=$form.limit_type label="アドバイザーのみ"}
			{$form.errors.limit_type|errormsg nofilter}
			<p class="notice">「アドバイザーのみ」を選択すると相談窓口を開設したアドバイザーのみ回答できます。</p>
		</div>*}

		<div class="infomsg" style="margin:10px 0;">
			<ul class="">
				<li>質問内容はすべて公開されます。</li>
			</ul>
		</div>

		<ul class="btn_area">
			<li><a id="mainform-btn" class="medium-btn green-btn green-btn-disabled" onclick="return false;">確認画面に進む</a></li>
			<li><span id="send-loader" class="hide-loader"></span></li>
		</ul>
		<div class="clear"></div>
	</div>
<div class="clear"></div>
</form>
</div>

{else}

<div style="margin-top:5px;text-align:center;">
<p style="margin-bottom:2px;">{$form.user.nickname} さんに相談するにはログインする必要があります。</p>
{include file="_parts/side_login_btn.tpl" is_fb_likebox=false}
</div>

{/if}

</div>
</div>

<div id="side-content">
{include file="_parts/side_user_view.tpl" user=$form.user user_rank=$form.user_rank}
{include file="_parts/ad/ad_side.tpl"}
</div>

<script type="text/javascript">
var sendButton = function()
{
	$('#mainform-btn').unbind('click', sendButton).addClass('green-btn-disabled');
	$('#send-loader').css('display', 'inline-block');
	$('#mainform').submit();
}
var checkAgree = function()
{
	if ($('#agree').prop('checked')) {
		$('#mainform-btn').removeProp('disabled').removeClass('green-btn-disabled').bind('click', sendButton);
	} else {
		$('#mainform-btn').prop('disabled', 'disabled').addClass('green-btn-disabled').unbind('click', sendButton);
	}
};
$(function(){
	//$('#agree').bind('click', checkAgree);
	//checkAgree();
	$('#mainform-btn').removeProp('disabled').removeClass('green-btn-disabled').bind('click', sendButton);
	Adviner.activeForm();
});
</script>
