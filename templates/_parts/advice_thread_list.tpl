<div class="balloon_item">
<div class="balloon_user">
<span class="profile-frame-50">{profile_img user=$user_set[$consult.consult_user_id] size=50}</span>
</div>
<div class="balloon_arrow"></div>
<div class="balloon_data">
<p class="balloon_title"><a href="/advice/{$consult.advice_id}/{$consult.consult_id}/" class="username">{$user_set[$consult.consult_user_id].nickname}</a></p>
<p class="balloon_body">{$consult.consult_body|makebody nofilter}</p>
{$permalink="`$REAL_URL`advice/`$consult.advice_id`/`$consult.consult_id`/"}
<ul class="balloon_navi">
<li class="balloon_navi_btn">{$permalink|social_fb_like:"70" nofilter}</li>
<li class="balloon_navi_last"><a href="/advice/{$consult.advice_id}/{$consult.consult_id}/" class="balloon_date">{$consult.createdate|datetime_t}</a></li>
</ul>
<div class="clear"></div>

<div class="balloon_thread" id="consult-thread-{$consult.consult_id}" style="display:none;">

{foreach item=rd from=$reply}

<div class="balloon_thread_item">
<div class="balloon_thread_user">
<span class="profile-frame-32">{profile_img user=$user_set[$rd.from_user_id] size=32}</span>
</div>
<div class="balloon_thread_data">
<p class="balloon_title">{$user_set[$rd.from_user_id]|username nofilter}</p>
<p class="balloon_body">{$rd.reply_body|makebody nofilter}</p>
{$permalink="`$REAL_URL`advice/`$consult.advice_id`/`$consult.consult_id`/`$rd.consult_reply_id`/"}
<ul class="balloon_navi">
<li class="balloon_navi_btn">{$permalink|social_fb_like:"70" nofilter}</li>
<li class="balloon_navi_last"><a href="/advice/{$consult.advice_id}/{$consult.consult_id}/{$rd.consult_reply_id}/" class="balloon_date">{$rd.createdate|datetime_t}</a></li>
</ul>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>

{/foreach}

{foreach item=rvd from=$review}

<div class="balloon_item">
<div class="balloon_thread_user">
<span class="profile-frame-32">
	{if $user_set[$rvd.consult_review_user_id].delete_flag==0 && $user_set[$rvd.consult_review_user_id].display_flag==0 && $rvd.secret_flag==0}
{profile_img user=$user_set[$rvd.consult_review_user_id] size=32}
	{else}
{profile_img user=null}
	{/if}
</span>
</div>
<div class="balloon_thread_data">
	{if $user_set[$rvd.consult_review_user_id].delete_flag==0 && $user_set[$rvd.consult_review_user_id].display_flag==0 && $rvd.secret_flag==0}
<p class="balloon_title">{$user_set[$rvd.consult_review_user_id]|username nofilter}</p>
	{else}
<p class="balloon_title gray">非公開</p>
	{/if}
<div style="margin-bottom:5px;">{$rvd.evaluate_type|review_star_mini nofilter}</div>
<p class="balloon_body">{$rvd.review_body|makebody nofilter}</p>
{$permalink="`$REAL_URL`review/`$rvd.consult_review_id`/"}
<ul class="balloon_navi">
<li class="balloon_navi_btn">{$permalink|social_fb_like:"70" nofilter}</li>
<li class="balloon_navi_last"><a href="/advice/review/{$rvd.consult_review_id}/" class="balloon_date">{$rvd.createdate|datetime_t}</a></li>
</ul>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>

{/foreach}

</div>

<div class="list-link" style="margin-top:10px;"><a href="/advice/{$consult.advice_id}/{$consult.consult_id}/" onclick="Adviner.clickOpenConsultThread('#consult-thread-{$consult.consult_id}', '/advice/{$consult.advice_id}/{$consult.consult_id}/', this);return false;" class="small-btn normal-btn">続きを見る</a></div>

</div>
<div class="clear"></div>
</div>
