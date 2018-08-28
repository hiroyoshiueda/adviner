<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-link" href="/advice/{$form.advice.advice_id}/">{$form.advice.advice_title}</a>&gt;
<a class="gnavi-link" href="/advice/{$form.advice.advice_id}/{$form.consult.consult_id}/">{$form.consult_user.nickname} さんの相談</a>&gt;
<a class="gnavi-last" href="{$base_url}">{$form.htitle}</a>
</div>

<div id="main-content">
<div class="normal-frame">

<p class="htitle bottom15">{$form.htitle}</p>

{*$var_pagetitle="`$form.advice.advice_title``$smarty.const.APP_CONST_SITE_TITLE3`"*}
{$var_pagebody="`$form.reply.reply_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
{$var_pagelink="/advice/`$form.advice_id`/`$form.consult_id`/`$form.consult_reply_id`/"}
{$var_permalink="`$REAL_URL`advice/`$form.advice_id`/`$form.consult_id`/`$form.consult_reply_id`/"}

<div class="balloon_item" style="padding:0;border:none;">
<div class="balloon_user">
	<span class="profile-frame-50">{profile_img user=$form.user size=50}</span>
</div>
<div class="balloon_data">
	<h1 class="balloon_body text-link"><a href="{$var_pagelink}">{$form.reply.reply_body|makebody:false nofilter}</a></h1>
	<ul class="balloon_navi">
		<li class="balloon_navi_btn">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
		<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
		<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
		<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$form.reply.createdate|datetime_t}</a></li>
	</ul>
	<div class="clear"></div>

	{*$var_pagetitle="`$form.advice.advice_title``$smarty.const.APP_CONST_SITE_TITLE3`"*}
	{$var_pagebody="`$form.consult.consult_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
	{$var_pagelink="/advice/`$form.advice.advice_id`/`$form.consult.consult_id`/"}
	{$var_permalink="`$REAL_URL`advice/`$form.advice.advice_id`/`$form.consult.consult_id`/"}

	<div class="balloon_quote">
		<div class="balloon_quote_user">
		<span class="profile-frame-32">{profile_img user=$form.consult_user size=32}</span>
		</div>
		<div class="balloon_quote_data">
			<p class="balloon_title">{$form.consult_user|username nofilter} さんの相談</p>
			<h2 class="balloon_body"><a href="{$var_pagelink}">{$form.consult.consult_body|makebody:false nofilter}</a></h2>
		</div>
		<div class="clear"></div>
		<ul class="balloon_navi">
			<li class="balloon_navi_btn">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
			<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
			<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
			<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$form.consult.createdate|datetime_t}</a></li>
		</ul>
		<div class="clear"></div>
	</div>

{if $form.advice.advice_user_id == $form.reply.from_user_id}
	<p class="balloon_title dmsg">&raquo; 相談窓口：<a href="/advice/{$form.advice.advice_id}/" title="{$form.advice.advice_title}">{$form.advice.advice_title}</a></p>
{else}
	<p class="balloon_title dmsg">&raquo; 相談窓口：<a href="/advice/{$form.advice.advice_id}/" title="{$form.advice.advice_title}">{$form.advice.advice_title}</a></p>
{/if}

</div>
<div class="clear"></div>
</div>

</div>
</div>

<div id="side-content">

{include file="_parts/side_user_view.tpl" user=$form.user user_rank=$form.user_rank}

</div>

<div class="clear"></div>
{include file="_parts/ad/ad_thread_footer_img.tpl"}
