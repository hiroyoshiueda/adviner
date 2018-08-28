<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<title>{$title}</title>
{include file="_parts/header_meta_tags.tpl"}
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="canonical" href="{$smarty.const.app_site_url}" />
<link rel="stylesheet" type="text/css" href="{$CSS_URL}/css/base.css?_={$smarty.const.APP_CONST_CSS_VER}" />
{include file="_parts/header_styles.tpl"}
<style>
body {
	background-color: #FFFDEF;
	background-color: #fbf9f2;
	padding: 20px;
}
#popup h1 {
	font-size: 16px;
}
#popup #popup_htitle_area {
	margin: 0 0 15px 0;
}
#popup #popup_list_frame {
	border: 1px solid #c9a977;
	background-color: #fff;
	width: 640px;
	height: 420px;
	overflow: auto;
}
</style>
{include file="_parts/header_js_const.tpl"}
<script src="{$PROTOCOL}://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="{$JS_URL}/js/base.js?_={$smarty.const.APP_CONST_JS_VER}"></script>
<script src="{$JS_URL}/js/adviner.js?_={$smarty.const.APP_CONST_JS_VER}"></script>
{include file="_parts/header_scripts.tpl"}
<!--[if lte IE 8]>
<script src="{$PROTOCOL}://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
{google_analytics}
</head>
<body id="popup">

<div id="popup_htitle_area">
<a onclick="parent.Adviner.closePopup();" style="float:right;" title="閉じる"><img src="/img/popup-close.png" width="12" height="14" alt="" /></a>
<h1>{$form.htitle}</h1>
</div>

{include file="$page_template"}

{include file="_parts/footer_scripts.tpl"}
</body>
</html>
