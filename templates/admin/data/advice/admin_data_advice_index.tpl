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
			<th></th>
			<th></th>
			<th>タイトル</th>
			<th>プロフィール</th>
			<th>登録日時</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$form.list item=d name="list"}
		<tr{if $d.delete_flag==1} style="background-color:#ccc;"{elseif $smarty.foreach.list.index % 2 == 0} style="background-color:#fff;"{else} style="background-color:#eee;"{/if}>
			<td>{$d.advice_id}</td>
			<td>{if $d.advice_status==0}<span style="color:#999;">停止中</span>{elseif $d.advice_status==1}受付中{elseif $d.advice_status==2}承認待ち{/if}</td>
			<td>{if $d.advice_status==2}<a href="accept?advice_id={$d.advice_id}">[承認する]</a>{/if}</td>
			<td>{if $d.advice_status==2}<a href="refuse?advice_id={$d.advice_id}">[却下する]</a>{/if}</td>
			<td><a href="{$smarty.const.app_site_url}advice/{$d.advice_id}/" target="_blank">{$d.advice_title}</a></td>
			<td><a href="{$smarty.const.app_site_url}profile/{$d.advice_user_id}/" target="_blank">プロフィール</a></td>
			<td>{$d.createdate|datetime_f}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{pager3 total=$form.total limit=$form.limit}