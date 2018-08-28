{if $form.errors.system}
<div id="alert-errormsg" class="errormsg">
<span class="error-icon"></span>&nbsp;{$form.errors.system|join:"<br />"}
</div>
{else if $form.errors}
<div id="alert-errormsg" class="errormsg" style="display:none;">
<span class="error-icon"></span>&nbsp;入力内容に誤りがあります
</div>
{else if $show_errormsg}
<div id="alert-errormsg" class="errormsg" style="display:none;">
<span class="error-icon"></span>&nbsp;{$show_errormsg}
</div>
{/if}