{foreach item=d from=$consult_list name="consult_list"}
<div class="list-item{if $smarty.foreach.consult_list.first && $is_top} list-top{/if}">
<div class="list-fromto-user">
	<ul class="list-fromto">
		<li><span class="profile-frame-32">{profile_img user=$form.user_set[$d.consult_user_id] size=32}</span></li>
		<li class="list-fromto-arrow"></li>
		<li><span class="profile-frame-32">{profile_img user=$form.user_set[$d.advice_user_id] size=32}</span></li>
	</ul>
	<div class="clear"></div>
</div>
<div class="list-fromto-info">
	<div class="list-message list-textlink"><a href="/advice/{$d.advice_id}/{$d.consult_id}/">{$d.consult_body|list_message:200 nofilter}</a></div>
	<div class="list-message dmsg"><a href="/advice/{$d.advice_id}/" title="{$d.advice_title}">{$d.advice_title}</a></div>
</div>
<div class="clear"></div>
</div>
{/foreach}