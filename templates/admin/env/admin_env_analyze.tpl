<form id="mainform" class="form-vertical" method="post" action="{$base_url}">
{include file="_parts/hidden.tpl" show_id=true}
	<fieldset>
		<legend>{$form.htitle}</legend>
		<p>Google Analyticsなどのアクセス解析サービスが提供するタグを設定することで、サイトのアクセス解析が行えます。</p>
		<div class="control-group">
			{*<label class="control-label">Radio buttons</label>*}
			<div class="controls">
				<textarea class="input-xxlarge" id="ENV_ANALYZE_TAG" name="ENV_ANALYZE_TAG" rows="10">{$form.ENV_ANALYZE_TAG}</textarea>
			</div>
		</div>
		<p class="help-block">入力されたタグは、&lt;head&gt;内に追加されます。</p>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary" id="submit-btn" data-loading-text="保存中...">保存する</button>
			<button type="button" class="btn btn-danger right" id="delete-btn" data-loading-text="削除中...">削除する</button>
		</div>
	</fieldset>
</form>
<script>
$(function(){
	$('#submit-btn').click(envSave);
	$('#delete-btn').click(envDelete);
});
</script>