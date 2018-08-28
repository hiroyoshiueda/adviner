{if $side_recent_list}
<div class="normal-frame">
<h3 class="side-title bottom10"><a href="/search/?sort=created">新着の相談窓口</a></h3>
<ul class="side-list">
{foreach item=d from=$side_recent_list name="side_recent_list"}
<li><div class="side-list-user"><span class="profile-frame-32">{profile_img user=$d size=32}</span></div>
	<div class="side-list-info">
		<p class="bottom5"><a href="/advice/{$d.advice_id}/" title="{$d.advice_title}">{$d.advice_title}</a></p>
		<div class="left_area">{$d.nickname}</div>
	{if $userInfo}
		<div class="follow-btn-frame" style="margin-top:0px;">{$d.follow_id|follow_btn:$d.advice_id:"a":$d.advice_title nofilter}</div>
	{/if}
		<div class="clear"></div>
	</div>
	<div class="clear"></div></li>
{/foreach}
</ul>
</div>
{/if}
