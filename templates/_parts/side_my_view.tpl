{if $user.user_id>0}
<div class="normal-frame">
<div id="side-profile">
<div id="side-profile-img">
<div class="profile-frame">
{profile_img user=$user size=70}
</div>
</div>
<div id="side-profile-info">
<ul>
<li class="bottom10">{$user|username nofilter}</li>
<li>{$user_rank.evaluate_ave|review_star nofilter}</li>
<li id="side-evaluate"><span class="evaluate-c">評価</span>&nbsp;<span class="evaluate-v">{$user_rank.evaluate_ave|string_format:"%.1f"}</span>
	<span class="evaluate-c">相談者</span>&nbsp;<span class="evaluate-v">{$user_rank.consult_total|string_format:"%d"}</span></li>
</ul>
</div>
<div class="clear"></div>
</div>
{if $user.user_id==$userInfo.id}
<div>
<a class="small-btn green-btn advice-btn" href="/user/advice/contact/"><span class="icon-advice-btn"></span>相談窓口を登録する</a>
</div>
{/if}
</div>
{/if}
