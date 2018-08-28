<form id="qa-form" method="post">
{include file="_parts/hidden.tpl" show_id=true}
<div id="please-advice-form">
<h2 class="please_advice_title">アドバイスください！<span> - 投稿された内容は公開されます。</span></h2>
<textarea id="please-advice-form-textarea" class="please_advice_input" name="please_body" placeholder="アドバイスしてほしい質問や相談を入力してください"></textarea>
<div id="please-advice-form-btn" class="please_advice_btn">
	<div class="please_advice_btn_left">
		{*<p class="please_advice_text">投稿された内容は公開されます。</p>*}
	</div>
	<div class="please_advice_btn_right">
		<ul class="please_advice_btn_list">
			<li><div class="check_fb_share"><label title="同時にFacebookでも共有する"><input type="checkbox" id="please-advice-form-fb-share" name="please_fb_share" value="facebook" checked="checked" /><span class="icon_fb_share"></span>Facebook</label></div></li>
			<li><a id="please-advice-form-post" class="small-btn green-btn">投稿する</a></li>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
</form>