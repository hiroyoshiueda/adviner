<div class="page-header">
	<h1>{$form.htitle}</h1>
</div>
<form id="selectform" method="post" action="{$base_url}">
<div style="margin-bottom:10px;">
	<a class="btn" href="form">追加する</a>
	<a id="saveorder-btn" class="btn btn-success" href="#">並び順を保存する</a>
	<strong>　絞り込み：</strong>{tag type="select" class="select-horizontal" name="faq_category_id" options=$categoryOptions blank="on" selected=$form.faq_category_id onchange="$('#selectform').submit();"}
</div>
</form>
<form id="mainform" method="post" action="saveorder">
<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			<th>ID</th>
			<th>表示</th>
			<th>並び順</th>
			<th>カテゴリー名</th>
			<th>質問</th>
			<th>最終更新日時</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$form.list item=d}
		<tr{if $d.display_flag==1} style="background-color:#ccc;"{/if}>
			<td>{$d.faq_id}</td>
			<td>{if $d.display_flag==1}<span style="font-weight:bold;color:#999;">非表示</span>{else}表示中{/if}</td>
			<td><input name="order_ids[]" value="{$d.faq_id}" type="hidden" />
				<input name="order_nums[]" value="{$d.order_num}" class="input-mini" type="text" size="5" /></td>
			<td>{$form.category_map[$d.faq_category_id].title}</td>
			<td><a href="form?id={$d.faq_id}">{$d.question}</a></td>
			<td>{$d.lastupdate|datetime_f}</td>
		</tr>
{/foreach}
	</tbody>
</table>
</form>
<script>
function doSaveOrderNum()
{
	if (confirm("「並び順」の番号順で並びを変更します。よろしいですか？\n※未入力は0番扱いとなります。")) {
		$("#mainform").submit();
	}
	return false;
}
$('#saveorder-btn').click(doSaveOrderNum);
</script>