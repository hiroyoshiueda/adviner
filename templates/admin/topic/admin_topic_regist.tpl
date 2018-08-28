{literal}
<script type="text/javascript">
//<![CDATA[
$(function(){
    $("#date").datepicker();
});
//function openImageDialog()
//{
//	$.openDOMWindow({
//		height: 500,
//		width: 750,
//		positionType: 'centered',
//		windowSource: 'iframe',
//		windowPadding: 0,
//		borderColor: '#b0da27',
//		borderSize: '4',
//		overlay: 1,
//		overlayColor:'#000',
//		overlayOpacity: '20',
//		windowSourceURL: '/file/image/popup'
//	});
//}
//function closeImageDialog()
//{
//	$.closeDOMWindow();
//}
//]]>
</script>
{/literal}
<h2>{$form.htitle}</h2>
<div style="padding:10px;">
<form id="mainform" method="post" action="save">
{include file="_parts/hidden.tpl"}
<table id="regist_tbl">
	<tbody>
		<tr>
			<th width="100">日付</th>
			<td><input type="text" id="date" name="date" value="{$form.date}" style="width:120px;" maxlength="10" /> <span class="notice">例） 2010-09-10</span>
				{$form.errors.date|@errormsg}</td>
		</tr>
		<tr>
			<th>タイトル</th>
			<td><input type="text" name="title" value="{$form.title}" style="width:400px;" />
				{$form.errors.title|@errormsg}</td>
		</tr>
		<tr>
			<th>内容</th>
			<td><textarea name="body" cols="70" rows="15">{$form.body}</textarea>
				{$form.errors.body|@errormsg}</td>
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
CKEDITOR.replace('body', editorConfig());
</script>
{/literal}