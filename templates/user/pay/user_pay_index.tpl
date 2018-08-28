<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom20">
<h1>{$form.htitle}</h1>
</div>

<p class="bottom5">有料相談の履歴</p>
<table class="bordered-table bottom20">
<tbody>
<tr>
	<th class="line" style="width:80px;">日付</th>
	<th class="line">相談窓口</th>
	<th class="line">アドバイザー</th>
	<th class="line" style="width:100px;">相談料</th>
</tr>
{foreach item=d from=$form.order_list}
<tr>
	<td>{$d.createdate|datetime_f}</td>
	<td><a href="/advice/{$d.advice_id}/" target="_blank">{$form.order_advice[$d.advice_id].advice_title}</a></td>
	<td>{$form.order_user[$d.advice_user_id]|username nofilter}</td>
	<td>{$d.amount|number_format} 円</td>
</tr>
{foreachelse}
<tr>
	<td colspan="4"><p class="dmsg">有料相談した履歴はありません。</p></td>
</tr>
{/foreach}
</tbody>
</table>

</div>
</div>

<div id="side-content">

{include file="_parts/side_mypage_menu.tpl"}

{include file="_parts/side_feedback_form.tpl"}

</div>
