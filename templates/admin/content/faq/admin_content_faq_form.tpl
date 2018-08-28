<div class="page-header">
	<h1>{$form.htitle}</h1>
</div>
<form id="mainform" class="form-horizontal" method="post" action="{$base_url}">
{include file="_parts/hidden.tpl"}
	<fieldset>
		<div class="control-group">
			<label class="control-label">カテゴリー</label>
			<div class="controls">
				{tag type="select" name="faq_category_id" options=$categoryOptions selected=$form.faq_category_id}
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">質問</label>
			<div class="controls">
				<input class="input-xxlarge" id="question" name="question" value="{$form.question}" type="text" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">回答</label>
			<div class="controls">
				<textarea class="input-xxlarge" id="answer" name="answer" rows="8">{$form.answer}</textarea>
				<p class="help-block">改行は自動的に改行タグ（&lt;br /&gt;）に変換されます。</p>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">並び順</label>
			<div class="controls">
				<input class="input-mini" id="order_num" name="order_num" value="{$form.order_num}" type="text" />
			</div>
		</div>
{if $form.id>0}
		<div class="control-group">
			<label class="control-label">表示</label>
			<div class="controls">
				{tag type="radio" name="display_flag" value="0" label="公開" checked=$form.display_flag id="display_flag_0"}
				{tag type="radio" name="display_flag" value="1" label="非公開" checked=$form.display_flag id="display_flag_1"}
			</div>
		</div>
{/if}
		<div class="form-actions">
{if $form.id>0}
			<button type="submit" class="btn btn-primary" id="submit-btn" data-loading-text="保存中...">保存する</button>
			<button type="button" class="btn" id="cancel-btn">キャンセル</button>
			<button type="button" class="btn btn-danger right" id="delete-btn" data-loading-text="削除中...">削除する</button>
{else}
			<button type="submit" class="btn btn-primary" id="submit-btn" data-loading-text="追加中...">追加する</button>
			<button type="button" class="btn" id="cancel-btn">キャンセル</button>
{/if}
		</div>
	</fieldset>
</form>
<script>
$(function(){
	$('#submit-btn').click(submitBtn);
	$('#cancel-btn').click(cancelBtn);
	$('#delete-btn').click(deleteBtn);
});
</script>
