<div id="main-content">
<div class="normal-frame">
<div class="input_form">

<div class="htitle_with_bar bottom15">
<h1>{$form.htitle}</h1>
</div>

<form id="mainform" method="post" action="{$base_url}">
{include file="_parts/hidden.tpl" show_id=true}

{include file="_parts/alert_successmsg.tpl" show_successmsg="保存しました。"}
{include file="_parts/alert_errormsg.tpl" show_errormsg="入力エラーがあります。"}

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
<label class="left_area">ウェブサイト</label>
<div class="right_area"><p class="notice">Facebookで変更</p></div>
<div class="clear"></div>
<div class="input_line">
<p>{$form.user.url|makebody:false nofilter}</p>
</div>
</div>

<div class="input_item">
<label class="left_area">自己紹介</label>
<div class="right_area"><p class="notice"><a href="#" id="profile_msg-edit-btn" onclick="editSetting('profile_msg');return false;" style="{if $form.is_edit_profile_msg=='1'}display:none;{/if}">変更</a><a href="#" id="profile_msg-cancel-btn" onclick="cancelSetting('profile_msg');return false;" style="{if $form.is_edit_profile_msg!='1'}display:none;{/if}">キャンセル</a></p></div>
<div class="clear"></div>
<div class="input_line">
<p id="profile_msg-view" style="{if $form.is_edit_profile_msg=='1'}display:none;{/if}">
{include file="user/setting/_profile_msg_view.tpl" user=$form.user}
</p>
<div id="profile_msg-edit" style="{if $form.is_edit_profile_msg!='1'}display:none;{/if}">
<textarea id="profile_msg-input" class="ime-on" name="profile_msg" rows="10" cols="10" style="width:538px;">{$form.profile_msg}</textarea>
<div style="margin-top:5px;">
<a href="#" onclick="return false;" id="profile_msg-btn" class="small-btn green-btn">保存する</a>&nbsp;<span id="profile_msg-loader" class="loader-frame"><span class="loader"></span></span>
</div>
</div>
</div>
</div>

<div class="input_item">
<label class="left_area">メール通知</label>
<div class="right_area"><p class="notice"><a href="#" id="mail_to-edit-btn" onclick="editSetting('mail_to');return false;" style="{if $form.is_edit_mail_to=='1'}display:none;{/if}">変更</a><a href="#" id="mail_to-cancel-btn" onclick="cancelSetting('mail_to');return false;" style="{if $form.is_edit_mail_to!='1'}display:none;{/if}">キャンセル</a></p></div>
<div class="clear"></div>
<div class="input_line">
<div id="mail_to-view" style="{if $form.is_edit_mail_to=='1'}display:none;{/if}">
{include file="user/setting/_mail_to_view.tpl" user=$form.user}
</div>
<div id="mail_to-edit" style="{if $form.is_edit_mail_to!='1'}display:none;{/if}">
<div>{tag type="checkbox" name="consult_mail_to" value="1" checked=$form.consult_mail_to label="相談者から相談された場合"}</div>
<div>{tag type="checkbox" name="consult_reply_to" value="1" checked=$form.consult_reply_to label="相談者から返信があった場合"}</div>
<div>{tag type="checkbox" name="advice_reply_to" value="1" checked=$form.advice_reply_to label="アドバイザーから返信があった場合"}</div>
<div>{tag type="checkbox" id="mail_to-input" name="consult_review_to" value="1" checked=$form.consult_review_to label="相談者から評価コメントがあった場合"}</div>
<div style="margin-top:5px;">
<a href="#" onclick="return false;" id="mail_to-btn" class="small-btn green-btn">保存する</a>&nbsp;<span id="mail_to-loader" class="loader-frame"><span class="loader"></span></span>
</div>
</div>
</div>
</div>

</form>

</div>
</div>
</div>

<div id="side-content">
{include file="_parts/side_setting_menu.tpl"}
</div>

<script type="text/javascript">
$(function(){
	$('#mail_to-btn').bind('click', saveSetting);
	$('#profile_msg-btn').bind('click', saveSetting);
});
function saveSetting()
{
	var idName = '';
	if ($(this).attr('id') == 'mail_to-btn') {
		idName = 'mail_to';
	} else if ($(this).attr('id') == 'profile_msg-btn') {
		idName = 'profile_msg';
	}
	var postData = getFormData(idName + '-edit');
	postData.target_col = idName;
	postData.security_token = $('#_security_token').val();

	$('#'+idName+'-btn').unbind('click', saveSetting).addClass('green-btn-disabled');
	$('#'+idName+'-loader').css('display', 'inline-block');
	$('p.errormsg-text').remove();

	ajaxPost('/user/setting/post_save_api', postData, function(data, dataType)
	{
		if (data.lists.result == 1) {
			if (data.lists.html != '') {
				$('#'+idName+'-view').html(data.lists.html);
				cancelSetting(idName);
				showTopAlert($('#alert-successmsg'));
			}
		} else if (data.lists.errmsg != '') {
			if (data.lists.errors) {
				if (data.lists.errors[idName]) {
					var err = data.lists.errors[idName].join("\n");
					$('#'+idName+'-input').after('<p class="errormsg-text">'+err+'</p>');
					showTopAlert($('#alert-errormsg'));
				}
			}
		}
		if (data.lists.security_token) {
			$('#_security_token').val(data.lists.security_token);
		}
		$('#'+idName+'-loader').css('display', 'none');
		$('#'+idName+'-btn').bind('click', saveSetting).removeClass('green-btn-disabled');
	});
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