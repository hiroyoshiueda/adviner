{if $review}

{*$var_pagetitle="評価コメント：`$advice.advice_title``$smarty.const.APP_CONST_SITE_TITLE4`"*}
{$var_pagebody="`$review.review_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
{$var_pagelink="/advice/review/`$review.consult_review_id`/"}
{$var_permalink="`$REAL_URL`advice/review/`$review.consult_review_id`/"}

<div class="balloon_thread_item{if $is_first} balloon_thread_item_top{/if} response_item">
<div class="balloon_thread_user">
	<span class="profile-frame-32">{profile_img user=$review_user size=32}</span>
</div>
<div class="balloon_thread_data">
	<div>{$review.evaluate_type|review_star_mini nofilter}</div>
{if $is_thread_open}
	<p class="balloon_body">{$review.review_body|makebody nofilter}</p>
{else}
	<p class="balloon_body">{$review.review_body|more_message:400 nofilter}</p>
{/if}
</div>
<div class="clear"></div>
<ul class="balloon_navi">
	<li class="balloon_navi_btn adviner_good">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
{if $latest_reply_id > 0 || $review.review_public_flag == 2}
	<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
	<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
{/if}
{if $review.consult_public_flag == 1 && $review.review_public_flag == 2}
	<li><a href="{$var_pagelink}" class="balloon_date">{$review.createdate|datetime_t}</a></li>
	<li class="balloon_navi_last"><span class="label running">公開中</span></li>
{else}
	<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$review.createdate|datetime_t}</a></li>
{/if}
</ul>
<div class="clear"></div>
</div>
{/if}
