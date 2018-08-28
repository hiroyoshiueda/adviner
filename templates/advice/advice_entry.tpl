<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-link" href="/profile/{$form.user.user_id}/" title="{$form.user.nickname}">{$form.user.nickname}</a>&gt;
<a class="gnavi-link" href="/advice/{$form.advice.advice_id}/" title="{$form.advice.advice_title}">{$form.advice.advice_title}</a>&gt;
<a class="gnavi-last" href="/advice/{$form.advice.advice_id}/entry" title="{$form.user.nickname} さんに相談する">{$form.user.nickname} さんに相談する</a>
</div>

<div id="main-content">

{include file="_parts/advice_frame.tpl" advice=$form.advice advice_user=$form.user category_set=$form.category_set show_btn=false show_social=false}

<div class="normal-frame">

{if $userInfo.id>0 && $userInfo.id==$form.user.user_id}

<h2 class="htitle bottom20">{$form.user.nickname} さんに相談する</h2>
<div class="infomsg" style="margin-top:15px;">本人には相談できません。</div>

{else if $userInfo.id>0}

<h2 class="htitle bottom15">{$form.user.nickname} さんに相談する</h2>

<div class="base-list bottom10">
{include file="_parts/alert_errormsg.tpl"}
<form id="mainform" method="post" action="confirm">
{include file="_parts/hidden.tpl" show_id=true}
	<div class="list-user">
		<span class="profile-frame-50">{profile_img user=$userInfo size=50}</span>
	</div>
	<div class="list-info">
		<div class="input_item">
			<label>{$userInfo.nickname}の相談内容</label>
			<textarea id="consult_body" name="consult_body" class="w490 ime-on" cols="20" rows="5">{$form.consult_body}</textarea>
			{$form.errors.consult_body|errormsg nofilter}
			<p class="notice">相談内容によっては回答されない場合があります。</p>
		</div>
{if $form.advice.charge_flag == "1"}
		<div class="infomsg" style="padding-top:5px;margin-bottom:10px;">
			<p class="bold bottom5">この相談窓口への相談は有料です。相談料 <span class="charge_price">{$form.advice.charge_price|number_format}</span> 円（税込）が課金されます。</p>
			<ul class="pin-line">
				<li>{$form.user.nickname} さんのアドバイスを閲覧する前にクレジットカード決済（PayPal）によるお支払いが必要となります。<span class="under bold">相談後のキャンセルはできません。</span></li>
				<li><span class="under">お支払いはアドバイスのあった日より7日以内にお願い致します。</span></li>
				<li>アドバイスのない場合は取引不成立となり、課金されません。</li>
				<li>7日以内にアドバイスのない場合も取引不成立となり、課金されません。</li>
				<li>有料相談の内容は非公開です。あなた と {$form.user.nickname} さんのみに公開されます。</li>
			</ul>
		</div>
{else if $form.advice.public_type == "1"}
{*		<div class="input_item">
			<div class="infomsg" style="padding:5px;margin-bottom:5px;">この相談窓口は非公開での相談が可能です。</div>
			<div>{tag id="public_flag" type="checkbox" name="public_flag" value="1" label="非公開で相談する" checked=$form.public_flag}</div>
			<p class="notice">非公開で相談すると内容は {$userInfo.nickname} と {$form.user.nickname} だけに公開されます。</p>
		</div>*}
{else}
		<div class="infomsg" style="padding-top:5px;margin-bottom:10px;">
			<p class="bold bottom5">この相談窓口への相談は無料です。</p>
			<ul class="pin-line">
				<li>相談内容はすべて公開されます。</li>
				<li>アドバイスのあった場合は、評価を登録してください。</li>
			</ul>
		</div>
{/if}

		<div class="input_item" style="margin:10px 0;">
		<p style="margin-bottom:4px;font-weight:bold;"><input id="agree" name="agree" value="1"{if $form.agree=="1"} checked="checked"{/if} type="checkbox" /> <a href="/terms/guide" target="_blank">相談・アドバイスする時のガイドライン</a>&nbsp;を読んで理解しました </p>
		{$form.errors.agree|@errormsg}
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
{if $form.advice.charge_flag != 1}
{include file="_parts/ad/ad_side.tpl"}
{/if}
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
	$('#agree').bind('click', checkAgree);
	checkAgree();
	Adviner.activeForm();
});
</script>
