<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom15"><h1>{$form.htitle}</h1></div>

<div class="infomsg" style="margin:10px 0 15px 0;">
{if $form.id>0}
	{if $form.advice_status==3}
<strong>まだ再申込みは完了していません。</strong>入力内容を確認し [再申込み] ボタンを押してください。
	{else}
<strong>まだ変更は完了していません。</strong>入力内容を確認し [変更する] ボタンを押してください。
	{/if}
{else}
<strong>まだ登録は完了していません。</strong>入力内容を確認し [登録を申込む] ボタンを押してください。<br />
お申込み後、運営事務局にて内容を確認し、承認したものから受付中として公開されます。
{/if}
</div>

{include file="_parts/alert_errormsg.tpl"}
<form id="mainform" method="post" action="save">
{include file="_parts/hidden.tpl"}

<div style="padding:0 15px;">

<div class="input_item">
<label>アドバイスできる分野</label>
{if $form.category_id>0}
<p>{$form.category_set[$form.category_id].cname}</p>
{/if}
</div>

<div class="input_item">
<label>アドバイスできること</label>
<p>{$form.advice_title}</p>
</div>

<div class="input_item">
<label>アドバイスできる詳細</label>
<p>{$form.advice_body|makebody nofilter}</p>
</div>

<div class="input_item">
<label>キーワード</label>
<p>{$form.advice_tag}</p>
</div>

{*<div class="input_item">
<label>非公開相談</label>
{if $form.public_type=="1"}
<p>非公開の相談も受け付ける</p>
{else}
<p>非公開の相談は受け付けない</p>
{/if}
</div>*}

<div class="input_item">
<label>有料でアドバイスする</label>
{if $form.charge_flag == "1"}
<p>有料（相談内容はすべて非公開）</p>
{else}
<p>無料（相談内容はすべて公開）</p>
{/if}
</div>

</div>

{if $form.charge_flag == "1"}
<div id="charge-frame" class="section_frame section_frame_on" style="margin-bottom:15px;">

<div class="input_item">
<label>有料相談一回の価格</label>
<p>{$form.charge_price|number_format} 円（税込）</p>
</div>

<div class="input_item">
<label>お支払いする金額</label>
<div>あなたへお支払いする金額：<span id="your-reward-text" class="your_reward">{$form.your_reward|number_format}</span> 円</div>
<div>利用手数料：<span id="system-fee-text">{$form.system_fee|number_format}</span> 円</div>
</div>

{*<div class="input_item">
<label>有料相談一回のアドバイス回数</label>
<p>{$AppConst.adviceChargeCount[$form.charge_count]}</p>
</div>*}

<div class="input_item">
<label>有料相談に関する詳細</label>
<p>{$form.charge_body|makebody nofilter}</p>
</div>

</div>
{/if}

<div class="confirm-btn-frame bottom10">
<div style="text-align:right;"><a onclick="$('#backform').submit();">&laquo; 入力した内容を修正する</a></div>
<ul class="btn_area">
	<li><a class="medium-btn green-btn" id="mainform-btn" onclick="return false;">{if $form.id>0}{if $form.advice_status==3}再申込み{else}変更する{/if}{else}登録を申込む{/if}</a></li>
	<li><span id="send-loader" class="hide-loader"></span></li>
</ul>
<div class="clear"></div>
</div>

</form>

<form id="backform" method="post" action="/user/advice/contact/{if $form.id>0}edit{/if}">
{include file="_parts/hidden.tpl"}
</form>

</div>
</div>

<div id="side-content">
{include file="user/advice/contact/_side_message.tpl"}
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
