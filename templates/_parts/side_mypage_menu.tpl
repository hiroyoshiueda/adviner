<div class="normal-frame">

<div id="side-profile">
<div id="side-profile-img" style="width:50px;">
<span class="profile-frame-50">
{profile_img user=$userInfo size=50}
</span>
</div>
<div id="side-profile-info">
<ul>
<li>{$userInfo|username nofilter}</li>
</ul>
</div>
<div class="clear"></div>
</div>

<ul class="menus">
<li{if $base_url=='/user/advice/'} class="active"{/if}><a href="/user/advice/"><span class="icon_mypage_menu advice"></span>相談窓口</a></li>
<li{if $base_url=='/user/advice/history/'} class="active"{/if}><a href="/user/advice/history/"><span class="icon_mypage_menu history"></span>相談されたこと</a></li>
<li{if $base_url=='/user/consult/'} class="active"{/if}><a href="/user/consult/"><span class="icon_mypage_menu consult"></span>相談したこと</a></li>
<li{if $base_url=='/user/following/advice'} class="active"{/if}><a href="/user/following/advice"><span class="icon_mypage_menu follow"></span>フォローした相談窓口</a></li>
<li{if $base_url=='/user/pay/'} class="active"{/if}><a href="/user/pay/"><span class="icon_mypage_menu pay"></span>支払い管理</a></li>
<li{if $base_url=='/user/reward/'} class="active"{/if}><a href="/user/reward/"><span class="icon_mypage_menu reward"></span>報酬管理</a></li>
</ul>

</div>