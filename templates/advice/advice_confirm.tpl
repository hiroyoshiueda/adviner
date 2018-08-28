<div id="main-content">

{include file="_parts/advice_frame.tpl" advice=$form.advice advice_user=$form.user category_set=$form.category_set show_btn=false show_social=false}

<div class="normal-frame">

<h2 class="htitle bottom15">{$form.user.nickname} さんに相談する</h2>

<div class="base-list">
	<form id="mainform" name="mainform" method="post" action="complete">
	{include file="_parts/hidden.tpl"}

	<div class="infomsg">
	<strong>まだ送信されていません。</strong>入力内容を確認し [送信する] ボタンを押してください。
	</div>

	<div class="list-user">
		<span class="profile-frame-50">{profile_img user=$userInfo size=50}</span>
	</div>
	<div class="list-info">
		<div class="input_item">
			<label>{$userInfo.nickname}の相談内容</label>
			<p>{$form.consult_body|makebody nofilter}</p>
		</div>
{if $form.advice.charge_flag == 1}
{else if $form.advice.public_type == 1}
{else}
		<div class="input_item">
			<label>無料相談</label>
			<p>相談内容はすべての人に公開されます。</p>
		</div>
{/if}
		<div class="confirm-btn-frame">
			<div style="text-align:right;margin-top:2px;"><a onclick="$('#backform').submit();">&laquo; 入力した内容を修正する</a></div>
{if $form.advice.charge_flag == 1}
			<div class="infomsg" style="padding-top:5px;margin-bottom:10px;margin-top:5px;">
				<p class="bold bottom5">この相談窓口への相談は有料です。相談料 <span class="charge_price">{$form.advice.charge_price|number_format}</span> 円（税込）が課金されます。</p>
				<ul class="pin-line bottom5">
					<li>{$form.user.nickname} さんのアドバイスを閲覧する前にクレジットカード決済（PayPal）によるお支払いが必要となります。<span class="under bold">相談後のキャンセルはできません。</span></li>
					<li><span class="under">お支払いはアドバイスのあった日より7日以内にお願い致します。</span></li>
					<li>アドバイスのない場合は取引不成立となり、課金されません。</li>
					<li>7日以内にアドバイスのない場合も取引不成立となり、課金されません。</li>
					<li>有料相談の内容は非公開です。あなた と {$form.user.nickname} さんのみに公開されます。</li>
				</ul>
				<p class="bold bottom5">よろしければ、[送信する] ボタンを押してください。</p>
				<ul class="btn_area">
					<li><a onclick="return false;" id="send-consult-btn" class="medium-btn green-btn">送信する</a></li>
					<li><span id="send-loader" class="hide-loader"></span></li>
				</ul>
				<div class="clear"></div>
			</div>
{else}
			<ul class="btn_area">
				<li><a onclick="return false;" id="send-consult-btn" class="medium-btn green-btn">送信する</a></li>
				<li><span id="send-loader" class="hide-loader"></span></li>
			</ul>
			<div class="clear"></div>
{/if}
		</div>
	</div>
	<div class="clear"></div>
	</form>
</div>

</div>
</div>

<div id="side-content">
{include file="_parts/side_user_view.tpl" user=$form.user user_rank=$form.user_rank}
</div>

<form id="backform" name="backform" method="post" action="/advice/{$form.advice_id}/entry">
{include file="_parts/hidden.tpl"}
</form>

<script type="text/javascript">
var sendConsult = function()
{
	$('#send-consult-btn').unbind('click', sendConsult).addClass('green-btn-disabled');
	$('#send-loader').css('display', 'inline-block');
	$('#mainform').submit();
}
$(function(){
	$('#send-consult-btn').bind('click', sendConsult);
});
</script>