{literal}
<script type="text/javascript">
//<![CDATA[
function doPay(uname, url)
{
	if (confirm(uname + "さんへの請求を完了にしますか？")) {
		jump(url);
	}
}
//]]>
</script>
{/literal}
{if $form.admin_pay_flag==0}
<h2>{$form.htitle}</h2>
<div style="padding:0 0 15px 15px;"><a href="payment?admin_pay_flag=1">→ 請求済み</a></div>
{else}
<h2>{$form.htitle}　＞　請求済み</h2>
{/if}
<table border="0" width="100%">
	<tbody>
		<tr>
			<td style="color:#666;">{pageinfo total=$form.total limit=$smarty.const.APP_CONST_ADMIN_PAGE_LIMIT}</td>
			<td align="right">{pager total=$form.total limit=$smarty.const.APP_CONST_ADMIN_PAGE_LIMIT}</td>
		</tr>
	</tbody>
</table>
<table id="list_table" border="0" width="100%">
	<tbody>
		<tr>
			<th width="40">ID</th>
			<th width="180">案件</th>
			<th width="160">装丁料 / 最終納品日</th>
			<th width="140">手数料振込期限</th>
			<th width="140">状態</th>
			<th>出版社</th>
			<th>デザイナー</th>
		</tr>
{foreach from=$form.list item=d}
	{assign var='p' value=$form.publisher[$d.publisher_id]}
	{assign var='u' value=$form.user[$d.user_id]}
		{*<tr{if $d.payment_deadline|get_timestamp:'23:59:59'<$smarty.now} style="background-color:#fcc;"{elseif $d.publisher_pay_flag==1 && $d.admin_pay_flag==0} style="background-color:#ffc;"{/if}>*}
		<tr>
			<td>{$d.project_id}</td>
			<td><a href="/project/detail-{$d.project_id}.html" target="_blank">{$d.book_type|project_title:$d.book_category}</a></td>
			<td><div>{$d.fee|number_format}</div>
				<div style="color:#666;">{$d.delivery_deadline|date_zen_f}</div></td>
			<td><div>{$d.payment_deadline|date_zen_f}</div></td>
			<td>{if $d.user_delivery_flag==0}制作中
				{elseif $d.user_delivery_flag==1 && $d.publisher_pay_flag==0}納品済<p style="color:#666;">{$d.user_delivery_date|date_zen_f}</p>
				{elseif $d.publisher_pay_flag==1}納品物受<p style="color:#666;">{$d.publisher_pay_date|date_zen_f}</p>{/if}</td>
			<td><div><a href="/admin/publisher/edit?id={$p.publisher_id}" target="_blank">{$p.corporate_name}</a></div>
				<div>{$p.division}</div>
				<div>{$p.post} {$p.name}</div>
			</td>
			<td><div><a href="/admin/user/edit?id={$u.user_id}" target="_blank">{$u.name}</a></div>
				{if $d.admin_pay_flag==0}<div style="padding-top:5px;"><input onclick="doPay('{$u.name}', 'paid?id={$d.propose_id}')" value="請求完了" type="button" /></div>{/if}
			</td>
		</tr>
{/foreach}
	</tbody>
</table>
<table border="0" width="100%">
	<tbody>
		<tr>
			<td style="color:#666;">{pageinfo total=$form.total limit=$smarty.const.APP_CONST_ADMIN_PAGE_LIMIT}</td>
			<td align="right">{pager total=$form.total limit=$smarty.const.APP_CONST_ADMIN_PAGE_LIMIT}</td>
		</tr>
	</tbody>
</table>
