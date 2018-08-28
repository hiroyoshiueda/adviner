{if $user.user_id>0}
<div class="normal-frame">
<div id="side-profile">
<div id="side-profile-img">
	<div class="profile-frame">{profile_img user=$user size=70}</div>
</div>
<div id="side-profile-info">
<ul>
<li>{$user|username nofilter}</li>
<li>{$user_rank.evaluate_ave|review_star nofilter}</li>
<li id="side-evaluate"><span class="evaluate-c">評価</span>&nbsp;<span class="evaluate-v">{$user_rank.evaluate_ave|string_format:"%.1f"}</span>
	<span class="evaluate-c">相談者</span>&nbsp;<span class="evaluate-v">{$user_rank.consult_total|string_format:"%d"}</span></li>
</ul>
</div>
<div class="clear"></div>

{if $detail!==false}
<div class="bottom15">
<a class="facebook-url-icon" href="{$user.open_url}" target="_blank">Facebookプロフィール</a>
</div>

<div class="bottom15">
<div class="bottom5"><h3 class="side-title"><span class="pin-icon right5"></span>自己紹介</h3></div>
<p>{$user.profile_msg|more_message:300 nofilter}</p>
</div>

<div class="bottom5">
<div class="bottom5"><h3 class="side-title"><span class="pin-icon right5"></span>ウェブサイト</h3></div>
<p>{$user.url|makebody nofilter}</p>
</div>
{/if}

</div>
</div>
{/if}
