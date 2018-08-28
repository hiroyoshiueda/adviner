<div style="width:600px;margin:0 auto 20px auto;" class="well">
<div class="page-header" style="margin-bottom:20px;">
	<h1><small>Adviner 管理画面</small></h1>
</div>
<form id="mainform" class="form-horizontal" method="post" action="{$ADMIN_PATH}/login">
{include file="_parts/hidden.tpl" show_id=true}
	<fieldset>
		<div class="control-group{$form.errors.adminlogin|errorclass}">
			<label class="control-label">ログインID</label>
			<div class="controls">
				<input class="input-xlarge" id="adminlogin" name="adminlogin" value="{$form.adminlogin}" type="text" />
			</div>
		</div>
		<div class="control-group{$form.errors.adminpassword|errorclass}">
			<label class="control-label">パスワード</label>
			<div class="controls">
				<input class="input-xlarge" id="adminpassword" name="adminpassword" value="{$form.adminpassword}" type="password" />
			</div>
		</div>
		<div class="form-actions" style="border:none;padding-top:0;">
			<button type="submit" class="btn btn-primary" id="submit-btn" data-loading-text="保存中...">ログイン</button>
		</div>
	</fieldset>
</form>
</div>
<script>
$(function(){
	$('#adminlogin').focus();
});
</script>
