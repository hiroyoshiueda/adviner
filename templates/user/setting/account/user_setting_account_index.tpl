<div id="main-content">
<div class="normal-frame">
<div class="input_form">

<div class="htitle_with_bar bottom15">
<h1>{$form.htitle}</h1>
</div>

<form id="mainform" method="post" action="save">
{include file="_parts/hidden.tpl" show_id=true}

{include file="_parts/alert_successmsg.tpl"}
{include file="_parts/alert_errormsg.tpl"}

<div class="input_item left_area right10">
<label>銀行名</label>
<div class="input">
<input class="w300 ime-on{$form.errors.bank_name|errorclass}" name="bank_name" value="{$form.bank_name}" type="text" size="20" />
{$form.errors.bank_name|errormsg nofilter}
</div>
</div>

<div class="input_item left_area">
<label>銀行コード</label>
<div class="input">
<input class="w50 ime-off{$form.errors.bank_code|errorclass}" name="bank_code" value="{$form.bank_code}" maxlength="4" type="text" size="20" />
{$form.errors.bank_code|errormsg nofilter}
</div>
</div>

<div class="clear"></div>

<div class="input_item left_area right10">
<label>支店名</label>
<div class="input">
<input class="w300 ime-on{$form.errors.branch_name|errorclass}" name="branch_name" value="{$form.branch_name}" type="text" size="20" />
{$form.errors.branch_name|errormsg nofilter}
</div>
</div>

<div class="input_item left_area">
<label>支店コード</label>
<div class="input">
<input class="w50 ime-off{$form.errors.branch_code|errorclass}" name="branch_code" value="{$form.branch_code}" maxlength="3" type="text" size="20" />
{$form.errors.branch_code|errormsg nofilter}
</div>
</div>

<div class="clear"></div>

<div class="input_item">
<label>口座番号</label>
<div class="input">
{tag type="select" class="{$form.errors.deposit_type|errorclass}" name="deposit_type" kvoptions=$AppConst.depositTypes selected=$form.deposit_type}&nbsp;
<input class="w100 ime-off{$form.errors.bank_number|errorclass}" name="bank_number" value="{$form.bank_number}" maxlength="7" type="text" size="20" />
{$form.errors.deposit_type|errormsg nofilter}
{$form.errors.bank_number|errormsg nofilter}
</div>
</div>

<div class="input_item">
<label>口座名義（全角カタカナ）</label>
<div class="input">
<input class="w300 ime-on{$form.errors.bank_holder|errorclass}" name="bank_holder" value="{$form.bank_holder}" type="text" size="20" />
{$form.errors.bank_holder|errormsg nofilter}
</div>
</div>

<div style="margin:15px 0;">
<ul class="pin-line">
<li>報酬の振込金額は、毎月月末に確定し翌々月20日に振り込まれます。</li>
<li>月末時点の残高が3,000円以上の場合、ご指定の口座に報酬が振り込まれます。</li>
<li>報酬の残高は「<a href="{$HTTPS_URL}user/reward/">報酬管理</a>」ページで確認することができます。</li>
<li>振り込みエラーの際は、振り込み手数料が報酬から差し引かれますのでご注意ください。</li>
<li>銀行コード、支店コードがわからない場合は、<a href="http://zengin.ajtw.net/" target="_blank">金融機関コード検索</a> をご利用ください。</li>
<li>銀行口座に海外の銀行はご指定できません。</li>
<li>ゆうちょ銀行の場合は、支店名に「店名（例：〇一八）」、支店コードに「店番（例：018）」を入力してください。</li>
</ul>
</div>

<ul class="btn_area">
<li><a onclick="return false;" id="save-btn" class="medium-btn green-btn">保存する</a></li>
<li><span id="send-loader" class="hide-loader"></span></li>
{if $form.user_account_id}
<li style="float:right;padding-top:5px;"><a onclick="return confirmUrl('delete', '口座情報を削除しますか？');">口座情報を削除する</a></li>
{/if}
</ul>
<div class="clear"></div>

</form>

</div>
</div>
</div>

<div id="side-content">
{include file="_parts/side_setting_menu.tpl"}
</div>

<script type="text/javascript">
var saveBtn = function()
{
	$('#save-btn').unbind('click', saveBtn).addClass('green-btn-disabled');
	$('#send-loader').css('display', 'block');
	$('#mainform').submit();
}
$(function(){
	$('#save-btn').bind('click', saveBtn);
	Adviner.activeForm();
	Adviner.showTopMessage();
});
</script>