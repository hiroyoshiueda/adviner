{literal}
<script type="text/javascript">
//<![CDATA[
function doSaveOrderNum()
{
	if (confirm("「並び順」の番号順で並びを保存します。よろしいですか？\n※未入力は0番扱いとなります。")) {
		$("#mainform").submit();
	}
}
//]]>
</script>
{/literal}
<h2>{$form.htitle}</h2>
<p style="margin-bottom:10px;"><input onclick="jump('regist');" style="width:120px;" value="新規登録" type="button" />
　<input onclick="doSaveOrderNum();" style="width:120px;" value="並び順を保存" type="button" /></p>
<form id="mainform" method="post" action="saveorder">
<table id="list_table" border="0" width="100%">
	<tbody>
		<tr>
			<th width="40">ID</th>
			<th width="80">表示</th>
			<th width="80">並び順</th>
			<th>カテゴリー名</th>
			<th width="120">最終更新日時</th>
		</tr>
{foreach from=$form.list item=d}
		<tr>
			<td>{$d.qa_category_id}</td>
			<td>{if $d.display_flag==1}<span style="font-weight:bold;color:#999;">非表示</span>{else}表示中{/if}</td>
			<td><input name="order_id[]" value="{$d.qa_category_id}" type="hidden" />
				<input name="order_num[]" value="{$d.order_num}" style="width:45px;text-align:right;" type="text" size="5" /></td>
			<td><a href="edit?id={$d.qa_category_id}">{$d.title}</a></td>
			<td>{$d.lastupdate|datetime_f}</td>
		</tr>
{/foreach}
	</tbody>
</table>
</form>
