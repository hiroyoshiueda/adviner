<div id="main-content">

<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-link" href="/contact/">お問い合わせ</a>&gt;
<a class="gnavi-last" href="{$HTTPS_URL}contact/form">{$form.htitle}</a>
</div>

<div id="contact">
<h1 class="page-title">{$form.htitle}</h1>
<div class="infomsg">
<p><strong>携帯電話メールアドレスでお問合せいただくお客様へ</strong><br />
迷惑メール拒否設定などをされている場合、返信メールがお手元に届かない場合がございますので<br />
[ {$smarty.const.APP_CONST_SITE_DOMAIN} ] を必ずドメイン指定受信ができるよう設定をお願いいたします</p>
</div>

{include file="_parts/alert_errormsg.tpl"}
{$form.sys_errors.msg|@errormsg}

<form name="mainform" method="post" action="{$HTTPS_URL}contact/complete" onsubmit="return doSubmit();">
{include file="_parts/hidden.tpl"}
<div class="column_wrapper">
	<label class="label-h" for="subject">
	 件名
	</label>
	<input id="subject" name="subject" style="width:580px;" class="ime-on" value="{$form.subject}" size="30" type="text" />
	{$form.errors.subject|@errormsg}
</div>
<div class="column_wrapper">
	<label class="label-h" for="body">
	 問い合わせ内容
	</label>
	<textarea id="body" name="body" style="width:580px;height:150px;" class="ime-on" rows="5" cols="10">{$form.body}</textarea>
	{$form.errors.body|@errormsg}
</div>
<div class="column_wrapper">
	<label class="label-h" for="username">
	 お名前
	</label>
	<input id="username" name="username" style="width:400px;" class="ime-on" value="{$form.username}" size="30" type="text" />
	{$form.errors.username|@errormsg}
</div>
<div class="column_wrapper">
	<label class="label-h" for="useremail">
	 メールアドレス
	</label>
	<input id="useremail" name="useremail" style="width:400px;" class="ime-off" value="{$form.useremail}" size="30" type="text" />
	{$form.errors.useremail|@errormsg}
	<p class="notice">※このメールアドレスに回答を返信します。お間違いのないように入力してください。</p>
</div>
<div id="btn-area"><input id="submit-btn" class="small-btn green-btn" type="submit" value="送信する" /></div>
</form>
</div>
</div>

<div id="side-content">

</div>
<script type="text/javascript">
function doSubmit()
{
	if (confirm("この内容で送信してよろしいですか？")) {
		$('#submit-btn').prop("disabled", "disabled");
		return true;
	}
	return false;
}
</script>