<div class="advice_frame normal-frame">
{if $show_status}
<div class="advice-frame-status">
{if $advice.advice_status==1}
	<span class="advice-status-ok">受付中</span>
{else}
	<span class="advice-status-stop">停止中</span>
{/if}
</div>
{/if}
<div class="advice_title_frame">
{if $title_tag}
	<{$title_tag} class="advice_title"><a href="/advice/{$advice.advice_id}/" title="{$advice.advice_title}">{$advice.advice_title}</a></{$title_tag}>
{else}
	<h1 class="advice_title"><a href="/advice/{$advice.advice_id}/" title="{$advice.advice_title}">{$advice.advice_title}</a></h1>
{/if}
</div>
<div class="left_area bottom10">
{if $advice.charge_flag == "1"}
	相談料：<span class="charge_price">{$advice.charge_price|number_format}</span> 円
{else}
	相談料：<span style="font-weight:bold;">無料</span>
{/if}
{if $advice.public_type == 1}
	<span class="private_msg"><img src="/img/icon_private.png" width="16" height="16" alt="非公開" />非公開相談</span>
{/if}
</div>
<div class="right_area" style="padding-top:2px;">
	<ul class="advice_info">
		<li class="advice_info_text">閲覧者</li>
		<li class="advice_info_num">{$advice.pv_total|string_format:"%d"}</li>
		<li class="advice_info_text">相談者</li>
		<li class="advice_info_num">{$advice.consult_total|string_format:"%d"}</li>
	</ul>
	<div class="clear"></div>
</div>
<div class="clear"></div>
<p class="bottom10">{$advice.advice_body|makebody nofilter}</p>
{if $advice.charge_flag == "1" && $advice.charge_body!=""}
<div class="charge_body">
	<p>{$advice.charge_body|makebody nofilter}</p>
</div>
{/if}
<p class="advice_category">{$advice.category_id|category_list:$category_set nofilter}</p>
{if $advice.advice_tag!=""}
<div style="margin-bottom:7px;">{$advice.advice_tag|tag_list nofilter}</div>
{/if}
{if $show_btn && ($advice.advice_status == 0 || $advice.advice_status == 1)}
	{if !$userInfo || ($userInfo.id>0 && $userInfo.id!=$advice.advice_user_id)}
<div>
		{if $advice.advice_status == 1}
			{if $advice.charge_flag == 1}
	<a href="{$HTTPS_URL}advice/{$advice.advice_id}/entry" class="small-btn green-btn consult-btn"><span class="icon-consult-btn"></span>{$advice_user.nickname} さんに相談する</a>
			{else}
	<a href="{$HTTP_URL}advice/{$advice.advice_id}/entry" class="small-btn green-btn consult-btn"><span class="icon-consult-btn"></span>{$advice_user.nickname} さんに相談する</a>
			{/if}
		{else}
	この相談窓口は現在受付ておりません。
		{/if}
</div>
	{/if}
{/if}
{if $show_social && ($advice.advice_status == 0 || $advice.advice_status == 1)}
{$var_pagetitle="[相談窓口]`$advice.advice_title` - `$advice_user.nickname``$smarty.const.APP_CONST_SITE_TITLE4`"}
{$var_pagelink="/advice/`$advice.advice_id`/"}
{$var_permalink="`$REAL_URL`advice/`$advice.advice_id`/"}
<ul class="social-list right" style="float:right;margin-top:10px;">
	<li class="adviner_good" style="height:22px;">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
	<li class="social_fb_like">{$var_permalink|social_fb_like nofilter}</li>
	<li class="social_twitter">{$var_permalink|social_twitter:$var_pagetitle:"horizontal" nofilter}</li>
	<li class="social_g_plusone last">{$var_permalink|social_g_plusone:"medium":"true" nofilter}</li>
{*	<li class="social_c_fb_share">{$var_permalink|social_c_fb_share}</li>
	<li class="social_c_twitter">{$var_permalink|social_c_twitter:$var_pagetitle}</li>*}
{*	<li class="social_fb_like">{$var_permalink|social_fb_like}</li>
	<li class="social_twitter">{$var_permalink|social_twitter:$var_pagetitle:"horizontal"}</li>
	<li style="width:60px;">{$var_permalink|social_g_plusone:"medium":"true"}</li>*}
</ul>
<div class="clear"></div>
{/if}
</div>
