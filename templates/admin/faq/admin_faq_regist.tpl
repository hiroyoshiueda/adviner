<h2>{$form.htitle}</h2>
<div style="padding:10px;">
<form id="mainform" method="post" action="save">
{include file="_parts/hidden.tpl"}
<table id="regist_tbl">
	<tbody>
		<tr>
			<th>カテゴリー</th>
			<td>{tag type="select" name="faq_category_id" options=$categoryOptions selected=$form.faq_category_id}
				{$form.errors.faq_category_id|@errormsg}</td>
		</tr>
		<tr>
			<th>質問</th>
			<td><input type="text" name="question" value="{$form.question}" style="width:600px;" />
				{$form.errors.question|@errormsg}</td>
		</tr>
		<tr>
			<th>回答</th>
			<td><textarea name="answer" rows="3" style="width:400px;">{$form.answer}</textarea>
				{$form.errors.answer|@errormsg}</td>
		</tr>
		<tr>
			<th>並び順</th>
			<td><input type="text" name="order_num" value="{$form.order_num}" style="width:45px;text-align:right;" />
				{$form.errors.order_num|@errormsg}</td>
		</tr>
{if $form.id>0}
		<tr>
			<th>表示</th>
			<td>{tag type="radio" name="display_flag" value="0" label="公開" checked=$form.display_flag style="width:auto;"}　
				{tag type="radio" name="display_flag" value="1" label="非公開" checked=$form.display_flag style="width:auto;"}</td>
		</tr>
{/if}
		<tr>
			<th></th>
			<td style="padding-top:20px;"><input type="submit" value="{if $form.id>0}保存する{else}登録する{/if}" style="width:100px;" />
				{if $form.id>0}<a href="#" onclick="deleteJump('delete?id={$form.id}');return false;" style="float:right;">&raquo; 削除する</a>{/if}</td>
		</tr>
	</tbody>
</table>
</form>
</div>
{literal}
<script type="text/javascript">
CKEDITOR.replace('answer', editorConfig());
</script>
{/literal}
