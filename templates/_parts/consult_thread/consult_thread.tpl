{*$var_pagetitle="`$advice.advice_title``$smarty.const.APP_CONST_SITE_TITLE4`"*}
{$var_pagebody="`$consult.consult_body|list_message:50:false``$smarty.const.APP_CONST_SITE_TITLE3`"}
{$var_pagelink="/advice/`$consult.advice_id`/`$consult.consult_id`/"}
{$var_permalink="`$REAL_URL`advice/`$consult.advice_id`/`$consult.consult_id`/"}

<div class="balloon_item"{if $is_first} style="border:none;"{/if}>
<div class="balloon_user">
	<span class="profile-frame-50">{profile_img user=$user_set[$consult.consult_user_id] size=50}</span>
</div>
<div class="balloon_data" id="consult-frame-{$consult.consult_id}">
{if $consult.consult_status == 1 && $consult.latest_reply_id == 0}
	{if $consult.advice_charge_flag == 1}
	<p class="balloon_chargemsg">この相談は有料相談（非公開）です。
		{if $userInfo && $consult.advice_user_id == $userInfo.id}
			{if $consult.review_state == 0}[アドバイス待ち]{else if $consult.advice_charge_flag == 1 && $consult.order_status != 2}[支払い待ち]{else}[支払い済み]{/if}
		{else if $userInfo && $consult.consult_user_id == $userInfo.id}
			{if $consult.review_state == 0}[アドバイス待ち]{else if $consult.advice_charge_flag == 1 && $consult.order_status != 2}[支払い待ち]{else}[支払い済み]{/if}
		{/if}
	</p>
	{else if $consult.public_flag == 1}
	<p class="balloon_infomsg">この相談は非公開です。</p>
	{else if $consult.consult_status == 1}
	<p class="balloon_infomsg">この相談はまだ非公開です。アドバイス後に公開されます。{if $consult.review_state == 0}[アドバイス待ち]{/if}</p>
	{/if}
{/if}
	<p class="bottom5">{$user_set[$consult.consult_user_id]|username nofilter} さんの相談</p>
	{if $is_thread_open}
		<p class="balloon_body">{$consult.consult_body|makebody nofilter}</p>
	{else}
		<p class="balloon_body">{$consult.consult_body|more_message:400 nofilter}</p>
	{/if}
	<ul class="balloon_navi">
{if $consult.latest_reply_id == 0}
		<li class="balloon_navi_btn adviner_good">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
{else}
		<li class="balloon_navi_btn adviner_good">{$var_pagelink|good_btn:$good:$userInfo nofilter}</li>
		<li class="balloon_navi_social social_c_fb_share">{$var_permalink|social_c_fb_share nofilter}</li>
		<li class="balloon_navi_social social_c_twitter">{$var_permalink|social_c_twitter:$var_pagebody nofilter}</li>
	{if $thread_link}
		<li><a href="{$var_pagelink}">相談スレッドを見る</a></li>
	{/if}
{/if}
		<li class="balloon_navi_last"><a href="{$var_pagelink}" class="balloon_date">{$consult.createdate|datetime_t}</a></li>
	</ul>
	<div class="clear"></div>

<div class="balloon_thread" id="reply-frame-{$consult.consult_id}">
<div id="reply-list-area-{$consult.consult_id}">
{* 返信: 有料相談は入金済のみ表示 *}
{$is_show_reply=true}
{if $reply_list}
	{if $consult.consult_status <= 1 && $consult.consult_user_id == $userInfo.id && $consult.advice_charge_flag == 1 && $consult.order_status != 2}
	<div class="chargemsg">
		<div class="left_area">
			<p>相談料：<span class="charge_price">{$consult.advice_charge_price|number_format}</span> 円（税込）</p>
		</div>
		<div class="right_area">
{if $smarty.const.APP_CONST_PROTOCOL == "https"}
			<a href="{$smarty.const.app_site_ssl_url}popup/payment/paypal?advice_id={$consult.advice_id}&consult_id={$consult.consult_id}" id="payment-{$consult.advice_id}-{$consult.consult_id}" onclick="return false;" class="small-btn green-btn click_popup_payment">お支払い</a>
{else}
			<a href="{$smarty.const.app_site_url}popup/payment/paypal?advice_id={$consult.advice_id}&consult_id={$consult.consult_id}" id="payment-{$consult.advice_id}-{$consult.consult_id}" onclick="return false;" class="small-btn green-btn click_popup_payment">お支払い</a>
{/if}
		</div>
		<div class="clear"></div>
		<p class="chargedesc">- 7日以内に相談料のお支払いをお願い致します。</p>
		<p class="chargedesc">- お支払いはクレジットカード決済（PayPal）のみとなります。</p>
		<p class="chargedesc">- お支払い後にアドバイスの内容が閲覧可能となります。</p>
	</div>
{$is_show_reply=false}
	{/if}
{if $is_show_reply}
	{foreach item=rd from=$reply_list name="thread_reply_list"}
{include file="_parts/consult_thread/consult_thread_reply_item.tpl" reply=$rd reply_user=$user_set[$rd.from_user_id] latest_reply_id=$consult.latest_reply_id is_first=$smarty.foreach.thread_reply_list.first}
	{/foreach}
{/if}
{/if}
{* 評価 *}
{if $is_show_reply && $review_list}
	{foreach item=rvd from=$review_list name="thread_review_list"}
{include file="_parts/consult_thread/consult_thread_review_item.tpl" review=$rvd review_user=$user_set[$rvd.consult_review_user_id] latest_reply_id=$consult.latest_reply_id}
	{/foreach}
{/if}
</div>

{if $consult.consult_status > 1}
	{if $consult.latest_reply_id > 0}
	{*<p class="balloon_finishmsg">この相談は終了しました</p>*}
	{else}
	<p class="balloon_finishmsg">この相談は非公開で終了しました</p>
	{/if}
{else if $userInfo && $consult.advice_user_id == $userInfo.id}
	{* アドバイザーアドバイス欄 *}
	<div id="reply-form-item-{$consult.consult_id}" class="balloon_thread_item balloon_thread_item_top">
		<div class="balloon_thread_user">
			<span class="profile-frame-32">{profile_img user=$userInfo size=32}</span>
		</div>
		<div class="balloon_thread_data">
			<div id="reply-form-area-{$consult.consult_id}">
				<textarea id="reply-form-text-{$consult.consult_id}" class="reply_form_input input_autosize" name="reply_form_input[]" cols="20" rows="5"></textarea>
	{if $consult.review_state > 0}
				<div class="balloon_thread_btn_area">
					<ul>
						<li><a class="small-btn green-btn reply_form_btn click_reply_form_button" href="#{$consult.consult_id}">アドバイス</a></li>
		{if $consult.public_flag == 2}
						<li><div class="check_fb_share"><label title="同時にFacebookでも共有する"><input type="checkbox" class="reply_fb_share" name="reply_fb_share[]" value="facebook" checked="checked" /><span class="icon_fb_share"></span>Facebook</label></div></li>
		{/if}
					</ul>
					<div class="clear"></div>
				</div>
	{else if $consult.public_flag == 1}
		{if $consult.advice_charge_flag == 1}
			<div class="chargemsg" style="margin-top:5px;padding-top:5px;">
				<p>相談料：<span class="charge_price">{$consult.advice_charge_price|number_format}</span> 円に対するアドバイスを送信してください。</p>
				<p class="chargedesc">- 相談料に応じた適切なアドバイスができない場合は、[アドバイスできない]ボタンを押して返信してください。</p>
		{/if}
				<div class="balloon_thread_btn_area">
					<ul>
						<li><a class="small-btn green-btn reply_form_btn click_reply_form_button" href="#{$consult.consult_id}" style="display:none;">アドバイス</a></li>
						<li><a class="small-btn gray-btn not_advice_form_btn click_not_advice_form_button" href="#{$consult.consult_id}">アドバイスできない</a></li>
						<li><a class="small-btn green-btn advice_form_btn click_advice_form_button" href="#{$consult.consult_id}">アドバイス</a></li>
					</ul>
					<div class="clear"></div>
				</div>
		{if $consult.advice_charge_flag == 1}</div>{/if}
	{else}
				<div class="balloon_thread_btn_area">
					<ul>
						<li><a class="small-btn green-btn reply_form_btn click_reply_form_button" href="#{$consult.consult_id}" style="display:none;">アドバイス</a></li>
						<li><a class="small-btn gray-btn not_advice_form_btn click_not_advice_form_button" href="#{$consult.consult_id}">アドバイスできない</a></li>
						<li><a class="small-btn green-btn advice_form_btn click_advice_form_button" href="#{$consult.consult_id}">アドバイスして公開</a></li>
		{if $consult.public_flag == 2}
						<li><div class="check_fb_share"><label title="同時にFacebookでも共有する"><input type="checkbox" class="reply_fb_share" name="reply_fb_share[]" value="facebook" checked="checked" /><span class="icon_fb_share"></span>Facebook</label></div></li>
		{/if}
					</ul>
					<div class="clear"></div>
				</div>
	{/if}
			</div>
		</div>
		<div class="clear"></div>
	</div>
{else if $is_show_reply && $userInfo && $consult.consult_user_id == $userInfo.id}
	{* 相談者返信欄 *}
	<div id="reply-form-item-{$consult.consult_id}" class="balloon_thread_item balloon_thread_item_top">
		<div class="balloon_thread_user">
			<span class="profile-frame-32">{profile_img user=$userInfo size=32}</span>
		</div>
		<div class="balloon_thread_data">
			<div id="reply-form-area-{$consult.consult_id}"{if $consult.review_state == 1} style="display:none;"{/if}>
				<textarea id="reply-form-text-{$consult.consult_id}" class="reply_form_input input_autosize" name="reply_form_input[]" cols="20" rows="5"></textarea>
				<div class="balloon_thread_btn_area">
					<ul>
	{if $consult.review_state == 0}
						<li><a class="small-btn green-btn reply_form_btn click_reply_form_button" href="#{$consult.consult_id}">送信する</a></li>
		{if $consult.latest_reply_id > 0 && $consult.public_flag == 2}
						<li><div class="check_fb_share"><label title="同時にFacebookでも共有する"><input type="checkbox" class="reply_fb_share" name="reply_fb_share[]" value="facebook" checked="checked" /><span class="icon_fb_share"></span>Facebook</label></div></li>
		{/if}
	{else if $consult.review_state == 1}
						<li><a class="small-btn green-btn reply_form_btn click_reply_form_button" href="#{$consult.consult_id}">送信する</a></li>
		{if $consult.public_flag == 2}
						<li><div class="check_fb_share"><label title="同時にFacebookでも共有する"><input type="checkbox" class="reply_fb_share" name="reply_fb_share[]" value="facebook" checked="checked" /><span class="icon_fb_share"></span>Facebook</label></div></li>
		{/if}
						<li class="btn_area_left"><a onclick="$('#reply-form-area-{$consult.consult_id}').hide();$('#review-form-area-{$consult.consult_id}').show();">評価する</a></li>
	{/if}
					</ul>
					<div class="clear"></div>
				</div>
			</div>
	{if $consult.review_state == 1}
			<div id="review-form-area-{$consult.consult_id}">
				{tag type="select" name="evaluate_type" id="review-form-evaluate-type-{$consult.consult_id}" class="select_evaluate_type" kvoptions=$AppConst.evaluateType selected=5}<br />
				<textarea id="review-form-text-{$consult.consult_id}" class="review_form_input input_autosize" name="reply_form_input[]" cols="20" rows="5"></textarea>
		{if $consult.public_flag == 1}
				<div class="review_form_public_flag" style="margin-top:2px;">
					<input type="checkbox" name="review_public_flag" id="review-form-public-flag-{$consult.consult_id}" value="2" />&nbsp;<label for="review-form-public-flag-{$consult.consult_id}">評価コメントは公開する</label>
				</div>
		{/if}
				<div class="balloon_thread_btn_area">
					<ul>
						<li><a class="small-btn blue-btn reply_form_btn click_review_form_button" href="#{$consult.consult_id}">評価する</a></li>
						<li><div class="check_fb_share"><label title="同時にFacebookでも共有する"><input type="checkbox" class="review_fb_share" name="review_fb_share[]" value="facebook" /><span class="icon_fb_share"></span>Facebook</label></div></li>
						<li class="btn_area_left"><a onclick="$('#review-form-area-{$consult.consult_id}').hide();$('#reply-form-area-{$consult.consult_id}').show();">更に返信する</a></li>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
	{/if}
		</div>
		<div class="clear"></div>
	</div>
{/if}
</div>

</div>
<div class="clear"></div>

</div>