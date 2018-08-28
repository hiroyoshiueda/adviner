{if $reply_set}
<div class="base-frmae normal-frame" style="margin-bottom:5px;">
	<div class="frame-message"><strong>最新のメッセージ{* - {if $reply_set[0]['from_user_id']==$userInfo['id']}あなた{else} さんの返信{/if}*}</strong></div>
	<div class="frame-message"><p>{$reply_set[0]['reply_body']|more_message:100|nl2br}</p></div>
	<div class="frame-message frame-date">{$reply_set[0]['createdate']|datetime_zen_f}</div>
</div>
{/if}