<div id="main-content">
<div class="normal-frame">

<div class="htitle_with_bar bottom15">
<h1>{$form.htitle}</h1>
</div>

<form id="mainform" method="post" action="{$base_url}">
{include file="_parts/hidden.tpl" show_id=true}

<div class="infomsg">
<p><strong>この内容で利用開始します。</strong></p>
<p>プロフィール写真、お名前、メールアドレスはFacebook上で変更してください。</p>
<p>自己紹介、メール通知はサインアップ後にも変更できます。</p>
</div>

{include file="_parts/alert_errormsg.tpl"}

<div class="input_item">
<label class="left_area">プロフィール写真</label>
<div class="right_area"><p class="notice">Facebookで変更</p></div>
<div class="clear"></div>
<div class="input_line">
<p>{profile_img user=$form.user size=70 is_href=false}</p>
</div>
</div>

<div class="input_item">
<label class="left_area">お名前</label>
<div class="right_area"><p class="notice">Facebookで変更</p></div>
<div class="clear"></div>
<div class="input_line">
<p>{$form.user.nickname}</p>
</div>
</div>

<div class="input_item">
<label class="left_area">メールアドレス</label>
<div class="right_area"><p class="notice">Facebookで変更</p></div>
<div class="clear"></div>
<div class="input_line">
<p>{$form.user.email}</p>
</div>
</div>

<div class="input_item">
<label class="left_area">自己紹介</label>
<div class="right_area"><a href="#" id="profile_msg-edit-btn" onclick="editSetting('profile_msg');return false;" style="{if $form.is_edit_profile_msg=='1'}display:none;{/if}">変更</a><a href="#" id="profile_msg-cancel-btn" onclick="cancelSetting('profile_msg');return false;" style="{if $form.is_edit_profile_msg!='1'}display:none;{/if}">キャンセル</a></div>
<div class="clear"></div>
<div class="input_line">
<p id="profile_msg-view" style="{if $form.is_edit_profile_msg=='1'}display:none;{/if}">{$form.user.profile_msg|makebody nofilter}</p>
<div id="profile_msg-edit" style="{if $form.is_edit_profile_msg!='1'}display:none;{/if}">
<textarea id="profile_msg-input" class="ime-on" name="profile_msg" rows="8" cols="10" style="width:538px;">{$form.profile_msg}</textarea>
{$form.errors.profile_msg|errormsg nofilter}
</div>
</div>
</div>

<div class="input_item">
<label class="left_area">メール通知</label>
<div class="right_area"><a href="#" id="mail_to-edit-btn" onclick="editSetting('mail_to');return false;" style="{if $form.is_edit_mail_to=='1'}display:none;{/if}">変更</a><a href="#" id="mail_to-cancel-btn" onclick="cancelSetting('mail_to');return false;" style="{if $form.is_edit_mail_to!='1'}display:none;{/if}">キャンセル</a></div>
<div class="clear"></div>
<div class="input_line">
<div id="mail_to-view" style="{if $form.is_edit_mail_to=='1'}display:none;{/if}">
{include file="user/setting/_mail_to_view.tpl" user=$form}
</div>
<div id="mail_to-edit" style="{if $form.is_edit_mail_to!='1'}display:none;{/if}">
<div>{tag type="checkbox" name="consult_mail_to" value="1" checked=$form.consult_mail_to label="相談者から相談された場合"}</div>
<div>{tag type="checkbox" name="consult_reply_to" value="1" checked=$form.consult_reply_to label="相談者から返信があった場合"}</div>
<div>{tag type="checkbox" name="advice_reply_to" value="1" checked=$form.advice_reply_to label="アドバイザーから返信があった場合"}</div>
<div>{tag type="checkbox" id="mail_to-input" name="consult_review_to" value="1" checked=$form.consult_review_to label="相談者から評価コメントがあった場合"}</div>
</div>
</div>
</div>

<div style="margin:10px 0;">
{tag type="checkbox" name="signup_fb_share" value="1" checked=$form.signup_fb_share label="Advinerを利用開始したことをFacebookでシェアする"}
{$form.errors.signup_fb_share|errormsg nofilter}
</div>

<div><a class="medium-btn green-btn" href="#" onclick="doSubmit();return false;">利用を開始する</a></div>

</form>

</div>
</div>

<div id="side-content">

</div>

<script type="text/javascript">
function doSubmit()
{
	$('#mainform').submit();
}
function editSetting(idName)
{
	$('#_is_edit_'+idName).val('1');
	$('#'+idName+'-view').css('display', 'none');
	$('#'+idName+'-edit').css('display', 'block');
	$('#'+idName+'-edit-btn').css('display', 'none');
	$('#'+idName+'-cancel-btn').css('display', 'block');
	$('#'+idName+'-input').focus();
}
function cancelSetting(idName)
{
	$('#_is_edit_'+idName).val('');
	$('#'+idName+'-edit').css('display', 'none');
	$('#'+idName+'-view').css('display', 'block');
	$('#'+idName+'-cancel-btn').css('display', 'none');
	$('#'+idName+'-edit-btn').css('display', 'block');
}
</script>