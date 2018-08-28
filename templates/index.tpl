<div id="top-pickup-frame">
<div class="pickup-area">
<h2 class="pickup-htitle"><a href="/search/" title="話題の相談窓口">話題の相談窓口</a></h2>
<ul id="login-pickup-list">
{foreach item=d from=$form.pickup_list name="pickup_list"}
<li class="login-pickup-block" style="display:none;">
<div class="login-pickup-item">
<div class="login-pickup-user">{profile_img user=$d size=30}</div>
<div class="login-pickup-info"><h2 class="login-pickup-title"><a href="/advice/{$d.advice_id}/" title="{$d.advice_title}">{$d.advice_title}</a></h2>
<p>{$d.nickname}</p>
</div>
<div class="clear"></div>
</div>
</li>
{/foreach}
</ul>
</div>

<div class="right_area">
<div style="background-color:#fff;width:300px;height:310px;margin:30px 10px 0 0;">
<iframe src="//www.facebook.com/plugins/likebox.php?href={"http://www.facebook.com/advinercom"|escape:"url"}&amp;width=300&amp;height=310&amp;colorscheme=light&amp;show_faces=true&amp;border_color=%23ebdfcd&amp;stream=false&amp;header=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:310px;" allowTransparency="true"></iframe>
</div>
</div>
<div class="clear"></div>
</div>
