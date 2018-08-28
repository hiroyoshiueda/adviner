<div class="balloon_item"{if $is_first} style="border:none;"{/if}>
<div class="balloon_user">
	<span class="profile-frame-50">{profile_img user=$review_user size=50}</span>
</div>
<div class="balloon_arrow"></div>
<div class="balloon_data" id="review-frame-{$consult.consult_id}">
	<p class="bottom5">{$review_user|username nofilter} さんの評価コメント{if $review.consult_public_flag == 1}<span class="dmsg">（相談内容は非公開）</span>{/if}</p>
	<div>{$review.evaluate_type|review_star_mini nofilter}</div>
	<p class="balloon_body">{$review.review_body|more_message:400 nofilter}</p>
{*$var_pagetitle="`$advice.advice_title``$smarty.const.APP_CONST_SITE_TITLE4`"*}
{$var_pagebody="`$review.review_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
{$var_pagelink="/advice/review/`$review.consult_review_id`/"}
{$var_permalink="`$REAL_URL`advice/review/`$review.consult_review_id`/"}
	<ul class="balloon_navi">
		<li class="balloon_navi_btn adviner_good">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
		<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
		<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
		<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$review.createdate|datetime_t}</a></li>
	</ul>
	<div class="clear"></div>
</div>
<div class="clear"></div>
</div>