<h2>{$form.htitle}</h2>
<p style="margin-bottom:10px;"><input onclick="jump('regist');" type="button" value="新規登録" /></p>
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
			<th width="80">表示</th>
			<th width="160">日付</th>
			<th>タイトル</th>
		</tr>
{foreach from=$form.list item=d}
		<tr>
			<td>{$d.topic_id}</td>
			<td>{if $d.display_flag==1}<span style="font-weight:bold;color:#999;">非表示</span>{else}表示中{/if}</td>
			<td>{$d.date|date_zen_f}</td>
			<td><a href="edit?id={$d.topic_id}">{$d.title}</a></td>
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
