{if $review}
<div class="feed_res_item{if $is_first} feed_res_item_top{/if} response_item">
<div class="feed_res_user">
<span class="profile-frame-32">{profile_img user=$review_user size=32}</span>
</div>
<div class="feed_res_data">
<div>{$review.evaluate_type|review_star_mini nofilter}</div>
<p class="feed_body">{$review.review_body|more_message:200 nofilter}</p>
<ul class="feed_navi">
<li>いいね</li>
<li class="feed_navi_last"><span class="feed_date">{$review.createdate|datetime_f}</span></li>
</ul>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
{/if}
