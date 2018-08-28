<h2>{$form.htitle}</h2>
<div style="padding:10px;">
{if $form.success}
<div class="successmsg">保存が完了しました。次回ログイン時より新しいログイン情報をお使いください。</div>
{/if}
<p>管理ツールにログインするための新しいログインID、パスワードを入力してください。</p>
<form id="mainform" method="post" action="save">
{include file="_parts/hidden.tpl"}
<table id="regist_tbl">
	<tbody>
		<tr>
			<th width="100">ログインID</th>
			<td><input type="text" name="login" value="{$form.login}" />
				{$form.errors.login|@errormsg}</td>
		</tr>
		<tr>
			<th>パスワード</th>
			<td><input type="password" name="password" value="{$form.password}" />
				{$form.errors.password|@errormsg}</td>
		</tr>
		<tr>
			<th></th>
			<td style="padding-top:20px;"><input type="submit" value="保存する" style="width:100px;" /></td>
		</tr>
	</tbody>
</table>
</form>
</div>
