<div class="list-item{if $is_first} list-top{/if}{if $reply_read[$item.consult_reply_id]} list-noread{/if}">
<div class="list-user">{profile_img user=$from_user size=50 is_href=false}</div>
<div class="list-info">
	<div class="list-message">{$from_user.nickname} さん</div>
	<div class="list-message"><p>{$item.reply_body|makebody nofilter}</p></div>
	<div class="list-message list-date">{$item.createdate|datetime_zen_f}</div>
</div>
<div class="clear"></div>
</div>