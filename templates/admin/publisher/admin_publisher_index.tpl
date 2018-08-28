{literal}
<script type="text/javascript">
//<![CDATA[
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
			<th width="90">利用状態</th>
			<th>会社名</th>
			<th>担当者</th>
			<th>連絡先</th>
			<th width="120">登録日時</th>
		</tr>
{foreach from=$form.list item=d}
		<tr{if $d.pay_flag==1} style="background-color:#ffc;"{/if}>
			<td>{$d.publisher_id}</td>
			<td>{if $d.status==0}利用中{elseif $d.status==1}仮登録{elseif $d.status==2}<span style="font-weight:bold;color:#999;">利用停止中</span>{/if}
				<p><a href="userauth?id={$d.publisher_id}" target="_blank">[ログイン]</a></p></td>
			<td><div>{$d.corporate_name}</div>
				<div style="font-size:88%;color:#666;">{$d.area}{$d.addr1}{$d.addr2}</div></td>
			<td><div><a href="edit?id={$d.publisher_id}">{$d.name}</a></div>
				<div style="font-size:88%;color:#666;">{$d.division}／{$d.post}</div></td>
			<td><div>{$d.email}</div>
				<div style="font-size:88%;color:#666;">{$d.tel}　緊急連絡先：{$d.mobile_tel}</div></td>
			<td>{$d.createdate|datetime_f}</td>
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
