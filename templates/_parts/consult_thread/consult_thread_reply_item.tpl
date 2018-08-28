{if $reply.reply_body != ""}

{*$var_pagetitle="[相談]`$advice.advice_title``$smarty.const.APP_CONST_SITE_TITLE4`"*}
{$var_pagebody="`$reply.reply_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
{$var_pagelink="/advice/`$reply.advice_id`/`$reply.consult_id`/`$reply.consult_reply_id`/"}
{$var_permalink="`$REAL_URL`advice/`$reply.advice_id`/`$reply.consult_id`/`$reply.consult_reply_id`/"}

<div class="balloon_thread_item{if $is_first} balloon_thread_item_top{/if} response_item">
<div class="balloon_thread_user">
	<span class="profile-frame-32">{profile_img user=$reply_user size=32}</span>
</div>
<div class="balloon_thread_data">
{if $is_thread_open}
	<p class="balloon_body">{$reply.reply_body|makebody nofilter}</p>
{else}
	<p class="balloon_body">{$reply.reply_body|more_message:400 nofilter}</p>
{/if}
</div>
<div class="clear"></div>
<ul class="balloon_navi">
	<li class="balloon_navi_btn adviner_good">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
{if $latest_reply_id > 0 }
	<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
	<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
{/if}
	<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$reply.createdate|datetime_t}</a></li>
</ul>
<div class="clear"></div>
</div>
{/if}
