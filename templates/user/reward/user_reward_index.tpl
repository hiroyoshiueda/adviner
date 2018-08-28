<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom20">
<h1>{$form.htitle}</h1>
</div>

{if $form.is_charge_advice}

{if $form.is_account_empty}
<div class="infomsg">報酬を受け取るためには、<a href="{$HTTPS_URL}user/setting/account/">銀行口座</a> の設定が必要となります。</div>
{/if}

<table class="bordered-table bottom5">
<tbody>
<tr>
<th style="width:150px;">今月の報酬金額</th>
<td class="right"><span class="charge_price">{$form.payment.reward_total|number_format}</span> 円<br />
（売上合計）{$form.payment.sales_total|number_format} 円</td>
</tr>
<tr>
<th>来月の振込予定金額</th>
<td class="right">{$form.payment.bank_total|number_format} 円<br />
（先月までの繰り越し合計）{$form.payment.carry_total|number_format} 円</td>
</tr>
</tbody>
</table>
<ul class="pin-line bottom20">
<li>月末時点の繰り越し分を含めた報酬金額の合計が3,000円未満の場合、お振込みは翌月以降に繰り越されます。</li>
</ul>

<p class="bottom5">有料アドバイス履歴（{if $form.year>0 && $form.month>0}{$form.year}年{$form.month}月{else}最新20件{/if}）</p>
{if $form.ym_list}
<ul class="year_month_list">
{foreach item=d from=$form.ym_list}
<li><a href="?year={$d.year}&month={$d.month}">{$d.year}年{$d.month}月</a></li>
{/foreach}
</ul>
<div class="clear"></div>
{/if}
<table class="bordered-table bottom20">
<tbody>
<tr>
	<th class="line" style="width:80px;">日付</th>
	<th class="line">相談窓口</th>
	<th class="line">相談者</th>
	<th class="line" style="width:100px;">報酬（売上）</th>
</tr>
{foreach item=d from=$form.order_list}
<tr>
	<td>{$d.createdate|datetime_f}</td>
	<td><a href="/advice/{$d.advice_id}/" target="_blank">{$form.order_advice[$d.advice_id].advice_title}</a></td>
	<td>{$form.order_user[$d.consult_user_id]|username nofilter}</td>
	<td><span>{$d.reward|number_format}</span> 円<br />（{$d.amount|number_format} 円）</td>
</tr>
{foreachelse}
<tr>
	<td colspan="4"><p class="dmsg">有料アドバイスした履歴はありません。</p></td>
</tr>
{/foreach}
</tbody>
</table>

{else}

<div class="infomsg">
<p>報酬を受け取るには有料アドバイスを行う相談窓口の開設が必要となります。</p>
<p>開設無料（審査有り）で、相談料を 100円 ～ 3,000円 の範囲で設定できます。</p>
</div>

{/if}

</div>
</div>

<div id="side-content">

{include file="_parts/side_mypage_menu.tpl"}

{include file="_parts/side_feedback_form.tpl"}

</div>
