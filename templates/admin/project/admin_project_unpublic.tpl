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
<h2>{$form.htitle}</h2>
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
			<th>案件</th>
			<th width="160">装丁料 / 最終納品日</th>
			<th width="160">公表日</th>
			<th width="60">提案数</th>
			<th>出版社</th>
		</tr>
{foreach from=$form.list item=d}
	{assign var='p' value=$form.publisher[$d.publisher_id]}
		<tr>
			<td>{$d.project_id}</td>
			<td><a href="/project/detail-{$d.project_id}.html" target="_blank">{$d.book_type|project_title:$d.book_category}</a></td>
			<td><div>{$d.fee|number_format}</div>
				<div style="color:#666;">{$d.delivery_deadline|date_zen_f}</div></td>
			<td><div>{$d.public_date|date_zen_f}</div></td>
			<td>{$d.cnt|number_format}</td>
			<td><div><a href="/admin/publisher/edit?id={$p.publisher_id}" target="_blank">{$p.corporate_name}</a></div>
				<div>{$p.division}</div>
				<div>{$p.post} {$p.name}</div>
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
