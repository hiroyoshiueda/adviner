{if $reply.reply_body != ""}
<div class="feed_res_item{if $is_first} feed_res_item_top{/if} response_item">
<div class="feed_res_user">
<span class="profile-frame-32">{profile_img user=$reply_user size=32}</span>
</div>
<div class="feed_res_data">
<p class="feed_body">{$reply.reply_body|more_message:200}</p>
<ul class="feed_navi">
<li>いいね</li>
{if $is_last}
{if $is_review}
<li class="feed_navi_open_review_form_button"><a class="click_open_review_form_button" title="評価欄を表示"><span>評価する</span>
{tag type="hidden" name="consult_ids[]" value=$reply.consult_id}</a></li>
{/if}
<li class="feed_navi_open_reply_form_button"><a class="click_open_reply_form_button" title="返信欄を表示"><span>返信する</span>
{tag type="hidden" name="consult_ids[]" value=$reply.consult_id}</a></li>
{/if}
<li class="feed_navi_last"><span class="feed_date">{$reply.createdate|datetime_f}</span></li>
</ul>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
{/if}
