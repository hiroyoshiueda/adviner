<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-last" href="{$base_url}">{$form.htitle}</a>
</div>

<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom15"><h1>{$form.htitle}</h1></div>
{if !$form.id || ($form.id>0 && $form.advice_status>1)}
<div class="infomsg" style="margin:10px 0 10px 0;">
<strong class="red">相談窓口の登録は審査制となっております。</strong><br />お申込み後、運営事務局にて内容を確認し、承認したものから受付中として公開されます。
</div>
{/if}
<p class="bottom10">あなたが相談を受付ける相談窓口を登録します。以下の項目を入力してください。</p>

{include file="_parts/alert_errormsg.tpl"}

<div class="bottom10">
<span class="required">※</span>&nbsp;必須項目
</div>

<form id="mainform" method="post" action="confirm">
{include file="_parts/hidden.tpl"}

<div class="input_item">
<label>アドバイスできる分野&nbsp;<span class="required">※</span></label>
{tag type="select-group" name="category_id" options=$categoryOptions groups=$AppConst.mainCategorys selected=$form.category_id blank="（未選択）"}
{$form.errors.category_id|errormsg nofilter}
</div>

<div class="input_item">
<label>アドバイスできること&nbsp;<span class="required">※</span></label>
<input name="advice_title" value="{$form.advice_title}" class="w560 ime-on" type="text" size="30" />
{$form.errors.advice_title|errormsg nofilter}
<p class="notice">全角40文字以内で入力してください</p>
</div>

<div class="input_item">
<label>アドバイスできる詳細&nbsp;<span class="required">※</span></label>
<textarea name="advice_body" class="w560 ime-on" cols="20" rows="5">{$form.advice_body}</textarea>
{$form.errors.advice_body|errormsg nofilter}
<p class="notice">全角800文字以内で入力してください</p>
</div>

<div class="input_item">
<label>キーワード</label>
<input name="advice_tag" value="{$form.advice_tag}" class="w560 ime-on" type="text" size="30" />
{$form.errors.advice_tag|errormsg nofilter}
<p class="notice">全角50文字以内。複数キーワードはスペースで区切って入力してください</p>
</div>

{*<div class="input_item" id="public_type-text">
<label>非公開相談</label>
<div class="input">
{tag type="checkbox" name="public_type" id="public_type-input" value="1" checked=$form.public_type label="非公開の相談も受付ける"}
{$form.errors.public_type|errormsg nofilter}
</div>
</div>*}

{*<div class="input_item">
<label>コメント</label>
{tag type="radio" name="comment_status" value="1" checked=$form.comment_status label="受付ける"}　
{tag type="radio" name="comment_status" value="0" checked=$form.comment_status label="受付けない"}
{$form.errors.comment_status|errormsg nofilter}
<p class="notice">- コメントはAdvinerに登録されているユーザーのみ投稿できます。</p>
</div>*}

<div class="input_item">
<label>有料でアドバイスする</label>
<div class="input">
{if $form.id>0}
<p>{if $form.charge_flag == "1"}有料{else}無料{/if}</p>
<div style="display:none;">
{tag type="radio" id="charge_flag_0" name="charge_flag" value="0" checked=$form.charge_flag label="無料"}　
{tag type="radio" id="charge_flag_1" name="charge_flag" value="1" checked=$form.charge_flag label="有料"}
</div>
{else}
{tag type="radio" id="charge_flag_0" name="charge_flag" value="0" checked=$form.charge_flag label="無料"}　
{tag type="radio" id="charge_flag_1" name="charge_flag" value="1" checked=$form.charge_flag label="有料"}
{$form.errors.charge_flag|errormsg nofilter}
{/if}
</div>
<ul class="pin-line">
<li>有料アドバイスについての説明は、<a href="/service/charge" target="_blank">こちら</a> をご覧ください。</li>
<li>無料・有料の区分は後から変更することはできません。</li>
<li>無料の場合、相談内容はすべて公開され、有料はすべて非公開となります。</li>
<li>有料の場合、<a href="/terms/charge_advice" target="_blank">有料アドバイス業務規約</a> に同意していただく必要があります。</li>
</ul>
</div>

<div id="charge-frame" class="section_frame">

<div class="input_item">
<label>有料相談一回の価格</label>
<input name="charge_price" id="charge_price-input" value="{$form.charge_price}" class="ime-off" style="width:100px;" type="text" size="30" /> 円
{$form.errors.charge_price|errormsg nofilter}
<p class="notice">- 税込価格を入力してください。</p>
<p class="notice">- 価格は 100円 ～ 3,000円の範囲内で設定してください。</p>
<p class="notice">- 価格の 30% が利用手数料として差引かれます。</p>
</div>

<div class="input_item">
<label>お支払いする金額</label>
<div>あなたへお支払いする金額：<span id="your-reward-text" class="your_reward">0</span> 円</div>
<div>利用手数料：<span id="system-fee-text">0</span> 円</div>
</div>

{*<div class="input_item">
<label>有料相談一回のアドバイス回数</label>
{tag type="select" name="charge_count" id="charge_count-input" kvoptions=$AppConst.adviceChargeCount selected=$form.charge_count style="width:auto;"}
{$form.errors.charge_count|errormsg nofilter}
<p class="notice">あなたからの回答がアドバイス回数に達した時点で、相談者は評価しか送信できない状態になります</p>
</div>*}

<div class="input_item">
<label>有料相談に関する詳細</label>
<textarea name="charge_body" id="charge_body-input" charge_countclass="ime-on" style="width:516px;" cols="20" rows="5">{$form.charge_body}</textarea>
{$form.errors.charge_body|errormsg nofilter}
<p class="notice">- 全角800文字以内で入力してください。</p>
<p class="notice">- 相談料に含まれる内容や条件、制約事項などを記入してください。</p>
</div>

<div class="input_item" style="margin-top:15px;">
<div class="box" style="font-weight:bold;">
<input id="agree2" name="agree2" value="1"{if $form.agree2=="1"} checked="checked"{/if} type="checkbox" /> <a href="/terms/charge_advice" target="_blank">有料アドバイス業務規約</a>&nbsp;に同意します
{$form.errors.agree2|@errormsg}
</div>
</div>

</div>

<div class="input_item" style="margin:15px 0;">
<p style="margin-bottom:4px;font-weight:bold;"><input id="agree" name="agree" value="1"{if $form.agree=="1"} checked="checked"{/if} type="checkbox" /> <a href="/terms/guide" target="_blank">相談・アドバイスする時のガイドライン</a>&nbsp;を読んで理解しました </p>
{$form.errors.agree|@errormsg}
</div>

<div class="bottom10">
<ul class="btn_area">
	<li><a id="mainform-btn" class="medium-btn green-btn green-btn-disabled" onclick="return false;">確認画面に進む</a></li>
	<li><span id="send-loader" class="hide-loader"></span></li>
</ul>
<div class="clear"></div>
</div>

</form>

</div>
</div>

<div id="side-content">

{include file="user/advice/contact/_side_message.tpl"}

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
	if ($('#agree').prop('checked') && ($('#charge_flag_0').attr('checked') || $('#charge_flag_1').attr('checked') && $('#agree2').prop('checked'))) {
		$('#mainform-btn').removeProp('disabled').removeClass('green-btn-disabled').bind('click', sendButton);
	} else {
		$('#mainform-btn').prop('disabled', 'disabled').addClass('green-btn-disabled').unbind('click', sendButton);
	}
};
var clickChargeFlag = function()
{
	if ($('#charge_flag_0').attr('checked')) {
		$('#charge-frame').removeClass('section_frame_on');
		$('#charge_price-input').attr('disabled', 'disabled');
		$('#charge_count-input').attr('disabled', 'disabled');
		$('#charge_body-input').attr('disabled', 'disabled');
		//$('#public_type-input').removeAttr('disabled');
		//$('#public_type-text').css('color', '');
		$('#agree2').attr('disabled', 'disabled');
	} else {
		$('#charge-frame').addClass('section_frame_on');
		$('#charge_price-input').removeAttr('disabled');
		$('#charge_count-input').removeAttr('disabled');
		$('#charge_body-input').removeAttr('disabled');
		//$('#public_type-input').attr('checked', 'checked').attr('disabled', 'disabled');
		//$('#public_type-text').css('color', '#999');
		$('#agree2').removeAttr('disabled');
	}
};
var calcYourReward = function()
{
	var yourReward = 0;
	var systemFee = 0;
	var p = $('#charge_price-input').val();
	if (p && !isNaN(p)) {
		yourReward = Math.floor(p * (CONST.USER_CHARGE_RATE / 100));
		systemFee = p - yourReward;
	}
	$('#your-reward-text').html(addFigure(yourReward));
	$('#system-fee-text').html(addFigure(systemFee));
};
$(function(){
	$('#agree').bind('click', checkAgree);
	$('#agree2').bind('click', checkAgree);
	checkAgree();
	$('#charge_flag_0,#charge_flag_1').click(clickChargeFlag);
	clickChargeFlag();
	$('#charge_price-input').blur(calcYourReward);
	calcYourReward();
	Adviner.activeForm();
});
</script>
