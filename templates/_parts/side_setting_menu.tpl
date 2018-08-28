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
<li{if $base_url=='/user/setting/'} class="active"{/if}><a href="{$HTTPS_URL}user/setting/"><span class="icon_setting_menu profile"></span>プロフィール設定</a></li>
<li{if $base_url=='/user/setting/account/'} class="active"{/if}><a href="{$HTTPS_URL}user/setting/account/"><span class="icon_setting_menu account"></span>銀行口座設定</a></li>
<li{if $base_url=='/user/setting/resign/'} class="active"{/if}><a href="{$HTTPS_URL}user/setting/resign/"><span class="icon_setting_menu resign"></span>退会する</a></li>
</ul>

</div>