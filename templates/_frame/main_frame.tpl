<!DOCTYPE html>
<html lang="ja" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta charset="UTF-8" />
<title>{$title}</title>
{include file="_parts/header_meta_tags.tpl"}
<meta name="keywords" content="{$keywords}" />
<meta name="description" content="{$description}" />
<meta property="og:title" content="{$title}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{$full_url}" />
<meta property="og:image" content="{$smarty.const.app_site_url}img/fb_page.png" />
<meta property="og:site_name" content="{$smarty.const.APP_CONST_SITE_TITLE_F}" />
<meta property="og:description" content="{$og_description}" />
<meta property="og:locale" content="ja_JP" />
<meta property="fb:admins" content="{$smarty.const.APP_CONST_FACEBOOK_USER_ID}" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="canonical" href="{$full_url}" />
<link rel="stylesheet" type="text/css" href="{$CSS_URL}/css/base.css?_={$smarty.const.APP_CONST_CSS_VER}" />
{*<link rel="stylesheet" type="text/css" href="{$JS_URL}/js/jquery/jquery.tipsy.css" />*}
{include file="_parts/header_styles.tpl"}
{include file="_parts/header_js_const.tpl"}
{*<script src="{$PROTOCOL}://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>*}
<script src="{$JS_URL}/js/jquery/jquery.min.js"></script>
<script src="{$JS_URL}/js/jquery/jquery.autosize-min.js"></script>
{*<script src="{$JS_URL}/js/jquery/jquery.js"></script>
<script src="{$JS_URL}/js/jquery/jquery.tipsy.js"></script>*}
<script src="{$JS_URL}/js/base.js?_={$smarty.const.APP_CONST_JS_VER}"></script>
<script src="{$JS_URL}/js/adviner.js?_={$smarty.const.APP_CONST_JS_VER}"></script>
<script src="{$JS_URL}/js/scrolltopcontrol.js"></script>
{include file="_parts/header_scripts.tpl"}
<!--[if lte IE 8]>
<script src="{$PROTOCOL}://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
{google_analytics}
</head>
<body>

{include file="_parts/fb_async_init.tpl"}

{if $userInfo}

<div id="header" class="header-home">
<div id="header-in">

<div id="header-logo">
<a href="{$HTTP_URL}" title="{$smarty.const.APP_CONST_SITE_TITLE_TOP}"><img src="/img/adviner-logo.png" width="140" height="33" alt="{$smarty.const.APP_CONST_SITE_TITLE_TOP}" /></a>
</div>

<div id="header-search">
{if $form.env_page_path == 'index/index'}
<form id="header-search-form-top" action="/search/">
{else}
<form id="header-search-form" action="/search/">
{/if}
<div id="header-search-frame"><div id="header-search-type" class="search-type-{$form.qtype}"></div><input id="header-search-q" name="q" value="{$form.q}" placeholder="相談窓口を検索" type="text" size="20" /><button id="header-search-btn" type="submit"><span>検索</span></button><div id="header-search-opt" class="search-opt-{$form.qopt}"{if $form.qtype!="1"} style="display:none;"{/if}></div><div class="clear"></div></div>
<input id="search-type" type="hidden" name="qtype" value="{$form.qtype}" />
<input id="search-opt" type="hidden" name="qopt" value="{$form.qopt}" />
</form>
</div>

<div id="menubar">
<ul id="menubar-list">
<li{if $form.env_page_path == "index/index"} class="menubar_selected"{/if}>
	<a href="{$HTTP_URL}" class="menubar-left" title="ホーム"><span>HOME</span></a><span class="menubar-arrow menubar-arrow-left"></span></li>
<li{if $form.env_class_path == "/user/advice" || $form.env_class_path == "/user/advice/history" || $form.env_class_path == "/user/consult" || $form.env_class_path == "/user/following"} class="menubar_selected"{/if}>
	<a href="/user/advice/" title="{$userInfo.nickname}のマイページ">
	<span class="profile-frame-20"><img src="{$userInfo.profile_s_path}" width="20" height="20" /></span>
	<span class="mypage_text">マイページ</span></a><span class="menubar-arrow"></span>
</li>
<li{if $form.env_class_path == "/user/setting" || $form.env_class_path == "/user/setting/account"} class="menubar_selected"{/if}>
	<a href="{$HTTPS_URL}user/setting/" title="設定"><span>設定</span></a><span class="menubar-arrow"></span>
</li>
<li><a><span>通知</span><span id="top-notice" class="top_notice_num top_notice_zero"><span>0</span></span></a></li>
<li><a id="header-facebook-logout-btn" class="menubar-right"><span>ログアウト</span></a></li>
</ul>
<div class="clear"></div>
<div id="header-notice-msg"></div>
</div>

</div>
</div>

{else if $form.env_page_path == "index/index"}

<div id="header" class="header-index">
<div id="header-in">

<div id="header-logo">
<a href="{$HTTP_URL}" title="{$smarty.const.APP_CONST_SITE_TITLE_TOP}"><img src="/img/adviner-logo.png" width="140" height="33" alt="{$smarty.const.APP_CONST_SITE_TITLE_TOP}" /></a>
</div>

<div id="menubar">
<ul id="menubar-list">
<li id="menubar-home"><a href="{$HTTP_URL}" class="menubar-left" title="ホーム">HOME</a></li>
<li{if $form.env_page_path == "service/index"} class="menubar_selected"{/if}><a href="/service/" title="Advinerとは">Advinerについて</a><span class="menubar-arrow"></span></li>
<li{if $form.env_page_path == "service/charge"} class="menubar_selected"{/if}><a href="/service/charge" title="有料アドバイスについて">有料アドバイスについて</a><span class="menubar-arrow"></span></li>
<li{if $form.env_page_path == "help/index"} class="menubar_selected"{/if}><a href="/help/" title="よくある質問">よくある質問</a><span class="menubar-arrow"></span></li>
{*<li><a href="http://blog.livedoor.jp/adviner/archives/cat_156383.html" target="_blank" title="使い方">使い方</a></li>*}
<li><a id="header-facebook-login-btn" class="menubar-right" title="ログイン">ログイン</a></li>
</ul>
<div class="clear"></div>
</div>

</div>

<div id="login-frame">
<div id="login-msg-frame">
<h1 class="index-title bottom10">相談とアドバイスで繋がろう！</h1>
<p class="bottom20">Adviner（アドバイナー）は、Facebookアカウントによる実名による質問や相談に対してアドバイスをもらうQ&Aサービスです。詳しくは <a href="/service/" title="Advinerについて">	&raquo; Advinerについて</a></p>
<h2 class="index-title bottom10">知識や経験を販売してみませんか？</h2>
<p>無料でのアドバイスに加え、有料でアドバイスをすることもできます。詳しくは <a href="/service/charge" title="有料アドバイスについて">	&raquo; 有料アドバイスについて</a></p>
<div id="login-social-frame">
<iframe src="http://www.facebook.com/plugins/like.php?href={"http://www.facebook.com/advinercom"|escape:"url"}&amp;show_faces=true&amp;width=650&amp;action=like&amp;colorscheme=light&amp;height=65&amp;appId={$smarty.const.APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY}" scrolling="no" frameborder="0" style="border:none;overflow:hidden;width:650px;height:65px;" allowTransparency="true"></iframe>
</div>
</div>
<div id="login-btn-frame">
{include file="_parts/side_login_btn.tpl" is_fb_likebox=false nanapi=true}
</div>
<div class="clear"></div>
</div>

</div>

{else}

<div id="header" class="header-home">
<div id="header-in">

<div id="header-logo">
<a href="{$HTTP_URL}" title="{$smarty.const.APP_CONST_SITE_TITLE_TOP}"><img src="/img/adviner-logo.png" width="140" height="33" alt="{$smarty.const.APP_CONST_SITE_TITLE_TOP}" /></a>
</div>

<div id="menubar">
<ul id="menubar-list">
<li id="menubar-home"><a href="{$HTTP_URL}" class="menubar-left" title="ホーム">HOME</a></li>
<li{if $form.env_page_path == "service/index"} class="menubar_selected"{/if}><a href="/service/" title="Advinerとは">Advinerについて</a><span class="menubar-arrow"></span></li>
<li{if $form.env_page_path == "service/charge"} class="menubar_selected"{/if}><a href="/service/charge" title="有料アドバイスについて">有料アドバイスについて</a><span class="menubar-arrow"></span></li>
<li{if $form.env_page_path == "help/index"} class="menubar_selected"{/if}><a href="/help/" title="よくある質問">よくある質問</a><span class="menubar-arrow"></span></li>
{*<li><a href="http://blog.livedoor.jp/adviner/archives/cat_156383.html" target="_blank" title="使い方">使い方</a></li>*}
<li><a id="header-facebook-login-btn" class="menubar-right" title="ログイン">ログイン</a></li>
</ul>
<div class="clear"></div>
</div>

</div>
</div>

{/if}

<div id="main">

{if $userInfo || $form.env_page_path != "index/index"}
<div id="content">
<div id="content-in">

{include file="$page_template"}

<div class="clear"></div>
</div>
</div>
{else}

{include file="$page_template"}

{/if}

</div>

<div id="footer">
<div id="footer-in">

<div id="footer-navi">

{if $form.category_list}
<div id="footer-category" class="bottom10">
<ul class="footer-category-list">
{foreach item=d from=$form.category_list}
{if $d.total>0}
<li><h3><a href="/category/{$d.main_category_id}/" title="{$d.main_cname}">{$d.main_cname}</a></h3></li>
{/if}
{/foreach}
</ul>
<div class="clear"></div>
</div>
{/if}

<div id="footer-menu" class="bottom15">
<ul class="footer-menu-list">
<li><a href="{$HTTP_URL}">HOME</a></li>
<li><a href="/service/" title="Advinerについて">Advinerについて</a></li>
<li><a href="/service/charge" title="有料アドバイスについて">有料アドバイスについて</a></li>
<li><a href="/help/" title="よくある質問">よくある質問</a></li>
{*<li><a href="http://nanapi.jp/search/q:Adviner" target="_blank" title="使い方">使い方</a></li>*}
<li style="border:none;"><a href="/search/" title="人気の相談窓口">人気の相談窓口</a></li>
{*<li style="border:none;"><a href="/search/?sort=created" title="新着の相談窓口">新着の相談窓口</a></li>*}
</ul>
<div class="clear"></div>
<ul class="footer-menu-list">
<li><a href="/terms/info" title="特定商取引法に基づく表記">特定商取引法に基づく表記</a></li>
<li><a href="/terms/rule" title="利用規約">利用規約</a></li>
<li><a href="/terms/charge_advice" title="有料アドバイス業務規約">有料アドバイス業務規約</a></li>
<li><a href="/terms/guide" title="ガイドライン">ガイドライン</a></li>
<li style="border:none;"><a href="http://www.e-sora.co.jp/" target="_blank" title="運営会社">運営会社</a></li>
</ul>
<div class="clear"></div>
</div>

<div id="footer-copyright">
<ul class="footer-copyright-list">
<li style="padding-top:2px;">Follow Adviner</li>
<li><a href="http://twitter.com/#!/advinercom" title="Adviner 公式Twitter" target="_blank"><img src="{$IMG_URL}/img/icon_twitter.png" style="vertical-align:middle;" width="16" height="16" alt="Adviner 公式Twitter" /></a></li>
<li><a href="http://www.facebook.com/advinercom" title="Adviner 公式facebookページ" target="_blank"><img src="{$IMG_URL}/img/icon_facebook.png" style="vertical-align:middle;" width="16" height="16" alt="Adviner 公式facebookページ" /></a></li>
<li style="padding-top:2px;">&copy; 2011 Esora Inc.</li>
</ul>
<div class="clear"></div>
</div>

</div>

<div id="footer-newtopic">
<h3><span class="pin-icon right5"></span>Advinerからのお知らせ</h3>
<dl class="footer-newtopic-list">

<dt>2012年2月2日</dt>
<dd><a href="http://blog.livedoor.jp/adviner/archives/5813261.html" target="_blank" title="リニューアルに伴う仕様変更のお知らせ">リニューアルに伴う仕様変更のお知らせ</a></dd>

<dt>2012年2月2日</dt>
<dd><a href="http://www.e-sora.co.jp/topics/201202/press/" target="_blank" title="知識や経験を販売できる有料アドバイス機能の提供を開始しました">知識や経験を販売できる有料アドバイス機能の提供を開始しました</a></dd>

<dt>2011年12月23日</dt>
<dd><a href="http://matome.naver.jp/odai/2132461707216888001" target="_blank" title="Adviner 2011年の人気相談窓口まとめ">Adviner 2011年の人気相談窓口まとめ</a></dd>

</dl>
</div>
<div class="clear"></div>

</div>
</div>

{include file="_parts/footer_scripts.tpl"}
</body>
</html>
