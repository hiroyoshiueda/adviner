<div id="main-content">
<div class="normal-frame">

{*<div class="infomsg"><a href="http://blog.livedoor.jp/adviner/archives/5813261.html" target="_blank" style="color:#665039;">2012/2/2 リニューアルに伴う仕様変更のお知らせ</a></div>*}

{if $form.recommend_set}
<div id="recommend-list" class="bottom15">
<h2 class="recommend-htitle">おすすめの相談窓口</h2>
{foreach item=aid from=$form.recommend_ids name="recommend_list"}
{assign var="d" value=$form.recommend_set[$aid]}
<div class="recommend-items"{if $smarty.foreach.recommend_list.index==0} style="margin-left:0;"{/if}>
	<div class="recommend-data">
		<h3><a href="/advice/{$d.advice_id}/" title="{$d.advice_title}">{$d.advice_title}</a></h3>
	</div>
	<div class="recommend-user">{profile_img user=$d size=50}</div>
{if $d.charge_flag == "1"}
	<div class="recommend-price"><p><span class="price-text">{$d.charge_price|number_format}</span> 円</p></div>
{else}
	<div class="recommend-price"><p>無料</p></div>
{/if}
</div>
{/foreach}
<div class="clear"></div>
</div>
{/if}

{*include file="_parts/please_advice_form.tpl"*}

<div class="htitle_tab bottom15">
<ul>
<li id="advice-tab"><a href="/#!/advice">相談窓口</a></li>
<li id="consult-tab"><a href="/#!/consult">公開された相談</a></li>
{*<li id="qa-tab"><a href="/#!/qa">Q&amp;A</a></li>*}
<li id="follow-tab"><a href="/#!/follow">フォローした相談</a></li>
<li id="action-tab"><a href="/#!/action">あなたの相談<span id="tab-action">{if $form.action_total>0}<span class="top_action_num">{$form.action_total}</span>{/if}</span></a></li>
</ul>
<div class="clear"></div>
</div>

<div id="top-feeder" class="base-list">
<div id="advice-list" style="display:none;"></div>
<div id="consult-list" style="display:none;"></div>
<div id="qa-list" style="display:none;"></div>
<div id="follow-list" style="display:none;">
	<p class="dmsg">フォローした相談窓口はありません。<a href="/search/">気になる相談窓口を探して</a>フォローしてみよう？</a></p>
</div>
<div id="action-list" style="display:none;">
	<p class="dmsg">進行中のあなたの相談はありません。</p>
</div>
<div id="loading" style="display:none;text-align:center;"><img src="/img/ajax-loader.gif" width="24" height="24" /></div>
<div class="list-more"><a href="javascript:;" id="advice-more" class="small-btn normal-btn" style="display:none;">続きを見る</a></div>
<div class="list-more"><a href="javascript:;" id="consult-more" class="small-btn normal-btn" style="display:none;">続きを見る</a></div>
<div class="list-more"><a href="javascript:;" id="qa-more" class="small-btn normal-btn" style="display:none;">続きを見る</a></div>
<div class="list-more"><a href="javascript:;" id="follow-more" class="small-btn normal-btn" style="display:none;">続きを見る</a></div>
<div class="list-more"><a href="javascript:;" id="action-more" class="small-btn normal-btn" style="display:none;">続きを見る</a></div>
</div>

</div>
</div>

<div id="side-content">

{include file="_parts/side_my_view.tpl" user=$userInfo user_rank=$userRank detail=false}

{include file="_parts/ad/ad_top_side.tpl"}

{include file="_parts/side_recent_list.tpl" side_recent_list=$form.side_recent_list}

<div class="normal-frame">
<h3 class="side-title bottom10">Social Media</h3>
<div>
<iframe src="//www.facebook.com/plugins/like.php?href={$REAL_URL|escape:"url"}&amp;show_faces=true&amp;width=270&amp;height=40&amp;action=like&amp;colorscheme=light&amp;appId={$smarty.const.APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY}" scrolling="no" frameborder="0" style="border:none;overflow:hidden;width:270px;height:40px;" allowTransparency="true"></iframe>
</div>
<div class="bottom5">
<ul class="social-list">
<li class="social_twitter">{$REAL_URL|social_twitter:$smarty.const.APP_CONST_SITE_TITLE:"horizontal" nofilter}</li>
<li class="social_g_plusone" style="width:70px;margin-right:10px;">{$REAL_URL|social_g_plusone:"medium":"true" nofilter}</li>
<li class="social_hatena">{$REAL_URL|social_hatena:$smarty.const.APP_CONST_SITE_TITLE:"standard" nofilter}</li>
</ul>
<div class="clear"></div>
</div>
</div>

<div class="normal-frame">
<h3 class="side-title"><a href="http://www.facebook.com/advinercom" target="_blank">Facebook Page</a></h3>
<div style="width:296px;margin:0 -13px;">
<iframe src="//www.facebook.com/plugins/likebox.php?href={"http://www.facebook.com/advinercom"|escape:"url"}&amp;width=296&amp;height=320&amp;colorscheme=light&amp;show_faces=true&amp;border_color=%23fbf9f2&amp;stream=false&amp;header=false" scrolling="no" frameborder="0" style="border:none;overflow:hidden;width:296px;height:320px;" allowTransparency="true"></iframe>
</div>
</div>

{include file="_parts/side_guide.tpl"}
{include file="_parts/side_feedback_form.tpl"}

</div>
