<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
{if $form.advice.advice_id>0}
<a class="gnavi-link" href="/advice/{$form.advice.advice_id}/">{$form.advice.advice_title}</a>&gt;
{/if}
<a class="gnavi-last" href="{$base_url}">{$form.htitle}</a>
</div>

<div id="main-content">
<div class="normal-frame">

<p class="htitle bottom10">{$form.htitle}</p>

{*$var_pagetitle="`$form.htitle``$smarty.const.APP_CONST_SITE_TITLE3`"*}
{$var_pagebody="`$form.consult.consult_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
{$var_pagelink="/advice/consult/`$form.consult_id`/"}
{$var_permalink="`$REAL_URL`advice/consult/`$form.consult_id`/"}

<div class="balloon_item" style="padding:0;border:none;">
<div class="balloon_user">
	<span class="profile-frame-50">{profile_img user=$form.consult size=50}</span>
</div>
<div class="balloon_data">
	<h1 class="balloon_body">{$form.consult.consult_body|makebody nofilter}</h1>
	<ul class="balloon_navi">
		<li class="balloon_navi_btn">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
		<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
		<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
		<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$form.consult.createdate|datetime_t}</a></li>
	</ul>
	<div class="clear"></div>
{if $form.advice}

	{*$var_pagetitle="`$form.advice.advice_title``$smarty.const.APP_CONST_SITE_TITLE3`"*}
	{*$var_pagebody="`$form.advice.advice_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"*}
	{$var_pagelink="/advice/`$form.advice.advice_id`/"}
	{$var_permalink="`$REAL_URL`advice/`$form.advice.advice_id`/"}

	<div class="balloon_quote">
	<div class="balloon_quote_user">
		<span class="profile-frame-32">{profile_img user=$form.advice size=32}</span>
	</div>
	<div class="balloon_quote_data">
		<p class="balloon_title">{$form.advice.nickname} さんの相談窓口</p>
		<p class="balloon_title">『<a href="/advice/{$form.advice.advice_id}/">{$form.advice.advice_title}</a>』</p>
		<p class="balloon_body">{$form.advice.advice_body|makebody nofilter</p>
		<ul class="balloon_navi">
			<li class="balloon_navi_btn">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
			<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$form.advice.createdate|datetime_t}</a></li>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	</div>
{else if $form.consult.consult_status == 0 && $form.consult.please_flag == 1}
	<div class="balloon_body" style="margin-top:8px;margin-bottom:10px;">
	{if $userInfo.id}
		<ul class="balloon_navi">
		{if $form.consult.consult_user_id != $userInfo.id}
			<li>この相談にはまだアドバイスがありません。</li>
			<li><a class="please_advice_btn" href="#{$form.consult.consult_id}">アドバイス</a></li>
			<li>してみませんか？</li>
		{/if}
		</ul>
		<div class="clear"></div>
	{else}
		<p class="bottom5">この相談にはまだアドバイスがありません。<br />アドバイスするにはログインしてください。</p>
		<div style="width:250px;">
		{include file="_parts/side_login_btn.tpl" is_fb_likebox=false}
		</div>
	{/if}
	</div>
{/if}
</div>
<div class="clear"></div>
</div>

</div>
</div>

<div id="side-content">

{include file="_parts/side_user_view.tpl" user=$form.consult user_rank=$form.user_rank}

</div>

<div class="clear"></div>
{include file="_parts/ad/ad_thread_footer_img.tpl"}