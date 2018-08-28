<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-link" href="/profile/{$form.user.user_id}/" title="{$form.user.nickname}">{$form.user.nickname}</a>&gt;
<a class="gnavi-last" href="/advice/{$form.advice.advice_id}/" title="{$form.advice.advice_title}">{$form.advice.advice_title}</a>
</div>

<div id="main-content">

{include file="_parts/advice_frame.tpl" advice=$form.advice advice_user=$form.user category_set=$form.category_set show_btn=true show_social=true}

<div class="normal-frame" style="margin-top:20px;">
{if $form.advice.advice_status == 0 || $form.advice.advice_status == 1}
<h2 class="htitle_with_line bottom5">相談されたこと</h2>
<div id="advice-consult-list">
{foreach item=d from=$form.consult_list name="consult_list"}
	{if $d.public_flag == 1}
	{foreach item=rvd from=$form.review_set[$d.consult_id] name="thread_review_list"}
{include file="_parts/consult_thread/consult_thread_review_public.tpl" review=$rvd review_user=$form.user_set[$rvd.consult_review_user_id] advice=$form.advice is_first=$smarty.foreach.consult_list.first is_thread_open=true}
	{/foreach}
	{else}
{include file="_parts/consult_thread/consult_thread.tpl" user_set=$form.user_set consult=$d reply_list=$form.reply_set[$d.consult_id] review_list=$form.review_set[$d.consult_id] advice=$form.advice is_first=$smarty.foreach.consult_list.first is_thread_open=true}
	{/if}
{foreachelse}
<p class="dmsg">公開された相談はありません。</p>
{/foreach}
</div>
{else}
<p class="dmsg">この相談窓口は承認待ちの為、まだ有効ではありません。</p>
{/if}
</div>

</div>

<div id="side-content">

{include file="_parts/side_user_view.tpl" user=$form.user user_rank=$form.user_rank}

{if $form.advice.charge_flag != 1}
{include file="_parts/ad/ad_side.tpl"}
{/if}

{include file="_parts/side_popular_list.tpl" side_popular_list=$form.side_popular_list}

</div>

<script type="text/javascript">
$(document).ready(function(){
	Adviner.bindConsultThreadEvent($('#advice-consult-list'));
});
</script>
