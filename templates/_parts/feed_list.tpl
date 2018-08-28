{foreach item=d from=$feed_list name="feed_list"}
<div class="balloon_item">

{*評価*}
{if $d.consult_review_id > 0}

{assign var='rvd' value=$form.review_set[$d.consult_review_id]}

<div class="balloon_user">
	<span class="profile-frame-50">{profile_img user=$form.user_set[$d.consult_review_user_id] size=50}</span>
</div>
<div class="balloon_data">
	<p class="balloon_title">{$form.user_set[$rvd.consult_review_user_id]|username nofilter}さんが{$form.user_set[$rvd.advice_user_id]|username nofilter}さんを評価しました。</p>
	<div>{$rvd.evaluate_type|review_star_mini nofilter}</div>
	<p class="balloon_body">{$rvd.review_body|more_message:200 nofilter}</p>
{*$var_pagetitle="`$form.advice_set[$d.advice_id].advice_title``$smarty.const.APP_CONST_SITE_TITLE3`"*}
{$var_pagebody="`$rvd.review_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
{$var_pagelink="/advice/review/`$rvd.consult_review_id`/"}
{$var_permalink="`$REAL_URL`advice/review/`$rvd.consult_review_id`/"}
	<ul class="balloon_navi">
		<li class="balloon_navi_btn adviner_good">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
		<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
		<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
		<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$rvd.createdate|datetime_t}</a></li>
	</ul>
	<div class="clear"></div>

	<div class="balloon_thread">
		<div class="balloon_thread_item">
			<div class="balloon_thread_user">
				<span class="profile-frame-32">{profile_img user=$form.user_set[$d.advice_user_id] size=32}</span>
			</div>
			<div class="balloon_thread_data">
				<p class="balloon_title"><a href="/advice/{$d.advice_id}/">{$form.advice_set[$d.advice_id].advice_title}</a></p>
				<p class="balloon_body">{$form.advice_set[$d.advice_id].advice_body|more_message:200 nofilter}</p>
		{if $rvd.consult_public_flag == 2}
				<div><a href="/advice/{$d.advice_id}/{$d.consult_id}/">&raquo; 相談スレッドを見る</a></div>
		{/if}
			</div>
			<div class="clear"></div>
		</div>
	</div>

</div>
<div class="clear"></div>

{*返信*}
{elseif $d.consult_reply_id > 0}

{assign var='rd' value=$form.reply_set[$d.consult_reply_id]}

<div class="balloon_user">
	<span class="profile-frame-50">{profile_img user=$form.user_set[$d.consult_reply_user_id] size=50}</span>
</div>
<div class="balloon_arrow"></div>
<div class="balloon_data">
{if $rd.reply_status == 1}
	<p class="balloon_title">{$form.user_set[$d.consult_reply_user_id]|username nofilter}さんが{$form.user_set[$rd.to_user_id]|username nofilter}さんに相談しました。</p>
{elseif $rd.reply_status == 11}
	<p class="balloon_title">{$form.user_set[$d.consult_reply_user_id]|username nofilter}さんが{$form.user_set[$rd.to_user_id]|username nofilter}さんにアドバイスしました。</p>
{/if}
	<p class="balloon_body">{$rd.reply_body|more_message:200 nofilter}</p>
{*$var_pagetitle="`$form.advice_set[$d.advice_id].advice_title``$smarty.const.APP_CONST_SITE_TITLE3`"*}
{$var_pagebody="`$rd.reply_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
{$var_pagelink="/advice/`$d.advice_id`/`$d.consult_id`/`$rd.consult_reply_id`/"}
{$var_permalink="`$REAL_URL`advice/`$d.advice_id`/`$d.consult_id`/`$rd.consult_reply_id`/"}
	<ul class="balloon_navi">
		<li class="balloon_navi_btn adviner_good">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
		<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
		<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
		<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$rd.createdate|datetime_t}</a></li>
	</ul>
	<div class="clear"></div>

	<div class="balloon_thread">
		<div class="balloon_thread_item">
			<div class="balloon_thread_user">
				<span class="profile-frame-32">{profile_img user=$form.user_set[$rd.to_user_id] size=32}</span>
			</div>
			<div class="balloon_thread_data">
				<p class="balloon_body">{$form.consult_set[$d.consult_id].consult_body|more_message:200}</p>
				<div><a href="/advice/{$d.advice_id}/{$d.consult_id}/">&raquo; 相談スレッドを見る</a></div>
			</div>
			<div class="clear"></div>
		</div>
	</div>

</div>
<div class="clear"></div>

{elseif $d.consult_id > 0}

{assign var='cd' value=$form.consult_set[$d.consult_id]}
{if $cd.consult_status == 0}
<div class="balloon_user">
	<span class="profile-frame-50">{profile_img user=$form.user_set[$d.consult_user_id] size=50}</span>
</div>
<div class="balloon_arrow"></div>
<div class="balloon_data">
	<p class="balloon_title">{$form.user_set[$d.consult_user_id]|username nofilter}さんがアドバイスくださいに投稿しました。</p>
	<p class="balloon_body">{$cd.consult_body|more_message:200}</p>
{*$var_pagetitle="`$form.user_set[$d.consult_user_id]|username`さんの相談`$smarty.const.APP_CONST_SITE_TITLE3`"*}
{$var_pagebody="`$cd.consult_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
{$var_pagelink="/advice/consult/`$d.consult_id`/"}
{$var_permalink="`$REAL_URL`advice/consult/`$d.consult_id`/"}
	<div class="balloon_body" style="margin-bottom:5px;">
		<ul class="balloon_navi">
			<li class="balloon_navi_btn adviner_good">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
			<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
			<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
		{if $d.consult_user_id != $userInfo.id}
			<li><a class="please_advice_btn" href="#{$d.consult_id}">アドバイスする</a></li>
		{/if}
			<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$cd.createdate|datetime_t}</a></li>
		</ul>
		<div class="clear"></div>
	</div>
</div>
<div class="clear"></div>
{/if}

{elseif $d.advice_id > 0}
	{if $form.advice_set[$d.advice_id]}
<div class="balloon_user">
	<span class="profile-frame-50">{profile_img user=$form.user_set[$d.advice_user_id] size=50}</span>
</div>
<div class="balloon_arrow"></div>
<div class="balloon_data" style="padding-bottom:15px;">
	<p class="balloon_title">{$form.user_set[$d.advice_user_id]|username nofilter}さんが相談窓口を登録しました。</p>
	<p class="balloon_title"><a href="/advice/{$d.advice_id}/">&raquo; {$form.advice_set[$d.advice_id].advice_title}</a></p>
	<p class="balloon_body">{$form.advice_set[$d.advice_id].advice_body|more_message:200}</p>
{$var_pagetitle="[相談窓口]`$form.advice_set[$d.advice_id].advice_title` - `$form.user_set[$d.advice_user_id].nickname``$smarty.const.APP_CONST_SITE_TITLE4`"}
{$var_pagelink="/advice/`$d.advice_id`/"}
{$var_permalink="`$REAL_URL`advice/`$d.advice_id`/"}
	<ul class="balloon_navi">
		<li class="balloon_navi_btn adviner_good">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
		<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
		<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagetitle nofilter}</li>
		<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$d.createdate|datetime_t}</a></li>
	</ul>
	<div class="clear"></div>
</div>
<div class="clear"></div>
	{/if}
{/if}
</div>
{/foreach}