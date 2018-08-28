<div class="page-header">
	<h1>{$form.htitle}</h1>
</div>
{pageinfo total=$form.total limit=$form.limit}
{pager3 total=$form.total limit=$form.limit}
<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			<th>ID</th>
			<th>利用状態</th>
			<th>ニックネーム</th>
			<th>メールアドレス</th>
			<th>Facebook</th>
			<th>登録日時</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$form.list item=d}
		<tr{if $d.delete_flag==1} style="background-color:#ccc;"{/if}>
			<td>{$d.user_id}</td>
			<td>{if $d.status==0}仮登録{elseif $d.status==1}利用中{elseif $d.status==2}<span style="font-weight:bold;color:#999;">利用停止中</span>{/if}
				<p><a href="/admin_userauth?uuid={$d.user_id}" target="_blank">[ログイン]</a></p>{if $d.delete_flag==1}<p>削除</p>{/if}</td>
			<td><img src="{$d.profile_s_path}" width="32" height="32" style="vertical-align:middle;" />&nbsp;<a href="{$smarty.const.app_site_url}profile/{$d.user_id}/" target="_blank">{$d.nickname}</a></td>
			<td>{$d.email}</td>
			<td>{$d.open_id}{if $d.login!=""}<div>{$d.login}</div>{/if}
				<a href="{$d.open_url}" target="_blank">facebook</a>
			</td>
			<td>{$d.createdate|datetime_f}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{pager3 total=$form.total limit=$form.limit}