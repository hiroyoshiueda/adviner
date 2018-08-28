{foreach item=d from=$advice_list name="advice_list"}
<div class="list-item{if $smarty.foreach.advice_list.first && $is_top} list-top{/if}">
{if $d.user_id}
<div class="list-user">
	<span class="profile-frame-50">{profile_img user=$d size=50}</span>
	<div class="username">{$d.nickname}</div>
</div>
{/if}
<div class="list-info{if !$d.user_id} list-info-width{/if}">
	<div class="list-message"><h3 class="list-title"><a href="/advice/{$d.advice_id}/" title="{$d.advice_title}">{$d.advice_title}</a></h3></div>
	<div class="list-message"><p>{$d.advice_body|list_message:100 nofilter}</p></div>
{if $d.advice_tag!=""}
	<div class="list-message list-tag">{$d.advice_tag|tag_list nofilter}</div>
{/if}
	<div class="list-message list-category">{$d.category_id|category_list:$form.category_set nofilter}</div>
	<div class="list-message left_area">
		<ul class="horizontal">
	{if $d.charge_flag == "1"}
			<li class="right5">相談料：<span class="charge_price">{$d.charge_price|number_format}</span> 円</li>
	{else}
			<li class="right5">相談料：<span style="font-weight:bold;">無料</span></li>
	{/if}
	{if $d.public_type == "1"}
			<li class="dmsg" style="padding-top:2px;"><img src="/img/icon_private.png" class="icon-top" width="16" height="16" alt="非公開" />非公開相談</li>
	{/if}
		</ul>
		<div class="clear"></div>
	</div>
	<div class="list-message right_area">
		<ul class="advice_info">
			<li class="advice_info_text">閲覧者</li>
			<li class="advice_info_num">{$d.pv_total|string_format:"%d"}</li>
			<li class="advice_info_text">相談者</li>
			<li class="advice_info_num">{$d.consult_total|string_format:"%d"}</li>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
{/foreach}