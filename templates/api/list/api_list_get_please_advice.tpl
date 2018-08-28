{foreach item=d from=$consult_list name="consult_list"}
<div class="balloon_item">
<div class="balloon_user">
	<span class="profile-frame-50">{profile_img user=$d size=50}</span>
</div>
<div class="balloon_arrow"></div>
<div class="balloon_data">
	<p class="balloon_title">{$d|username nofilter} さんの相談</p>
	<p class="balloon_body">{$d.consult_body|more_message:200 nofilter}</p>
{*$var_pagetitle="`$d|username`さんの相談`$smarty.const.APP_CONST_SITE_TITLE3`"*}
{$var_pagebody="`$d.consult_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
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
			<li class="balloon_navi_last"><a class="balloon_date" href="{$var_pagelink}">{$d.createdate|datetime_t}</a></li>
		</ul>
		<div class="clear"></div>
	</div>
</div>
<div class="clear"></div>
</div>
{/foreach}