<div id="main-content">
<div class="normal-frame">
<div class="input_form">

<div class="htitle_with_bar bottom15">
<h1>{$form.htitle}</h1>
</div>

<form id="mainform" method="post" action="user_resign">
{include file="_parts/hidden.tpl" show_id=true}

<div class="infomsg" style="margin:15px 0;">
<ul class="pin-line">
<li>Advinerを退会されますと、有料相談窓口を開設されたアドバイザーの報酬はクリアされますのでご注意ください。</li>
<li>また、退会後のデータ復旧はできませんのでご注意ください。</li>
<li>登録時にFacebookで許可したAdvinerアプリは、Facebook上で削除してください。</li>
</ul>
</div>

<p class="bottom5">[退会する]ボタンを押すと確認画面が表示されます。</p>

<ul class="btn_area">
<li><a onclick="return false;" id="save-btn" class="medium-btn green-btn">退会する</a></li>
<li><span id="send-loader" class="hide-loader"></span></li>
</ul>
<div class="clear"></div>

</form>

</div>
</div>
</div>

<div id="side-content">
{include file="_parts/side_setting_menu.tpl"}
</div>

<script type="text/javascript">
var saveBtn = function()
{
	if (confirm("注意事項を確認した上で退会します。よろしいですか？")) {
		$('#save-btn').unbind('click', saveBtn).addClass('green-btn-disabled');
		$('#send-loader').css('display', 'block');
		$('#mainform').submit();
	}
}
$(function(){
	$('#save-btn').bind('click', saveBtn);
});
</script>