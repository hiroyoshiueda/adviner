<form id="mainform" class="form-vertical" method="post" action="{$base_url}">
{include file="_parts/hidden.tpl" show_id=true}
	<fieldset>
		<legend>{$form.htitle}</legend>
		<div class="control-group">
			{*<label class="control-label">Radio buttons</label>*}
			<div class="controls">
				<label class="checkbox">
					{tag type="checkbox" name="ENV_FORCE_HTTPS" value="1" checked=$form.ENV_FORCE_HTTPS}
	                強制的にSSL接続を使用する
	                <p class="help-block">ご利用ユーザーの接続に対してSSL接続を強制します。</p>
				</label>
				<label class="checkbox">
					{tag type="checkbox" name="ENV_ADMIN_HTTPS" value="1" checked=$form.ENV_ADMIN_HTTPS}
	                この管理画面の接続をSSL接続のみに限定する
	                <p class="help-block">この管理画面への接続をSSL接続以外は禁止します。</p>
				</label>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary" id="submit-btn" data-loading-text="保存中...">保存する</button>
		</div>
	</fieldset>
</form>
<script>
$(function(){
	$('#submit-btn').click(envSave);
});
</script>
