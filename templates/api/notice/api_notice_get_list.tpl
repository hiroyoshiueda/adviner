{if $notice_list}
<div id="header-notice-list">

{foreach item=d from=$notice_list name="notice_list"}

{assign var='ad' value=$form.advice_set[$d.advice_id]}
{assign var='ud' value=$form.user_set[$d.from_user_id]}

<div class="header_notice_item{if $smarty.foreach.notice_list.first} header_notice_item_top{/if}{if $d.status == 0} header_notice_item_new{/if}">

<div class="notice_user">
<span class="notice_img">{profile_img user=$ud size=32 is_href=false}</span>
</div>

{if $d.notice_type == 1}

<div class="notice_data">
{if $ad.charge_flag == 1}
<a class="notice_main" href="{$smarty.const.app_site_ssl_url}advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんから有料相談されました。</a>
{else}
<a class="notice_main" href="/advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんから相談されました。</a>
{/if}
<div class="notice_date">{$d.createdate|datetime_t}</div>
</div>

{elseif $d.notice_type == 2}

<div class="notice_data">
{if $ad.charge_flag == 1}
<a class="notice_main" href="{$smarty.const.app_site_ssl_url}advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんからアドバイスがありました。</a>
{else}
<a class="notice_main" href="/advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんからアドバイスがありました。</a>
{/if}
<div class="notice_date">{$d.createdate|datetime_t}</div>
</div>

{elseif $d.notice_type == 3}

<div class="notice_data">
{if $ad.charge_flag == 1}
<a class="notice_main" href="{$smarty.const.app_site_ssl_url}advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんはアドバイスできませんでした。</a>
{else}
<a class="notice_main" href="/advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんはアドバイスできませんでした。</a>
{/if}
<div class="notice_date">{$d.createdate|datetime_t}</div>
</div>

{elseif $d.notice_type == 4}

<div class="notice_data">
{if $ad.charge_flag == 1}
<a class="notice_main" href="{$smarty.const.app_site_ssl_url}advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんから返信がありました。</a>
{else}
<a class="notice_main" href="/advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんから返信がありました。</a>
{/if}
<div class="notice_date">{$d.createdate|datetime_t}</div>
</div>

{elseif $d.notice_type == 5}

<div class="notice_data">
{if $ad.charge_flag == 1}
<a class="notice_main" href="{$smarty.const.app_site_ssl_url}advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんから評価されました。</a>
{else}
<a class="notice_main" href="/advice/{$d.advice_id}/{$d.consult_id}/">
<span class="notice_username">{$ud.nickname}</span> さんから評価されました。</a>
{/if}
<div class="notice_date">{$d.createdate|datetime_t}</div>
</div>

{/if}

<div class="clear"></div>

</div>
{/foreach}

</div>
{/if}