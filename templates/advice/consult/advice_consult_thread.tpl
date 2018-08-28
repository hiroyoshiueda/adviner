<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-link" href="/profile/{$form.advice.advice_user_id}/" title="$form.user_set[$form.advice.advice_user_id].nickname}">{$form.user_set[$form.advice.advice_user_id].nickname}</a>&gt;
<a class="gnavi-link" href="/advice/{$form.advice.advice_id}/">{$form.advice.advice_title}</a>&gt;
<a class="gnavi-last" href="/advice/{$form.advice.advice_id}/{$form.consult.consult_id}/">{$form.htitle}</a>
</div>

<div id="main-content">

{include file="_parts/advice_frame.tpl" advice=$form.advice advice_user=$form.user_set[$form.advice.advice_user_id] category_set=$form.category_set show_btn=false show_social=true}

{if $userInfo && ($userInfo.id == $form.consult.consult_user_id || $userInfo.id == $form.consult.advice_user_id)}

	<div class="normal-frame">

		{include file="_parts/alert_successmsg.tpl"}

		<div id="advice-consult-list">
		{include file="_parts/consult_thread/consult_thread.tpl" user_set=$form.user_set consult=$form.consult reply_list=$form.reply_list review_list=$form.review_list is_first=true is_thread_open=true}
		</div>

	</div>

{else if $form.consult.latest_reply_id>0}

	<div class="normal-frame">

		<div id="advice-consult-list">
		{include file="_parts/consult_thread/consult_thread.tpl" user_set=$form.user_set consult=$form.consult reply_list=$form.reply_list review_list=$form.review_list is_first=true is_thread_open=true}
		</div>

	</div>

{else if $form.consult.consult_status == 1}
	{if $form.consult.public_flag == 1}
<div class="normal-frame">
<p>この相談は非公開です。</p>
</div>
	{else}
<div class="normal-frame">
<p>この相談スレッドは相談中です。{$form.user_set[$form.advice.advice_user_id]|username nofilter}さんのアドバイス後に公開されます。</p>
</div>
	{/if}
{else}
	{if $form.consult.public_flag == 1}
<div class="normal-frame">
<p>この相談は非公開のまま終了しました。</p>
</div>
	{else}
<div class="normal-frame">
<p>この相談は非公開のまま終了しました。</p>
</div>
	{/if}
{/if}

</div>

<div id="side-content">

{include file="_parts/side_user_view.tpl" user=$form.user_set[$form.advice.advice_user_id] user_rank=$form.advice_user_rank}

{if $form.advice.charge_flag != "1"}
{include file="_parts/ad/ad_side.tpl"}
{/if}

</div>

<script type="text/javascript">
$(document).ready(function(){
	Adviner.bindOpenPopupPayment('.click_popup_payment');
	Adviner.bindConsultThreadEvent($('#advice-consult-list'));
	//Adviner.FBParse($('#advice-consult-list'));
});
</script>