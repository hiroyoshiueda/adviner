<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$title}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<!--[if lt IE 9]>
<script src="{$PROTOCOL}://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link href="{$CSS_URL}/admin/css/bootstrap.css" rel="stylesheet">
<link href="{$CSS_URL}/admin/css/bootstrap-responsive.css" rel="stylesheet">
<style type="text/css">
body {
	padding-top: 60px;
	padding-bottom: 40px;
}
.sidebar-nav {
	padding: 9px 0;
}
.form-actions {
	/*width: 600px;*/
	padding-right: 0;
	padding-left: 0;
}
.control-group.error .help-block,
.control-group.error .help-inline {
	color: #999999;
}
.control-group.error .input-error {
	color: #b94a48;
}
.btn.right {
	float: right;
}
.pageinfo {
	color: #666666;
	text-align: right;
}
select.select-horizontal {
	margin-bottom: 2px;
}
</style>
<script src="{$PROTOCOL}://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="{$JS_URL}/admin/js/bootstrap.min.js"></script>
<script src="{$JS_URL}/js/base.js"></script>
<script src="{$JS_URL}/js/admin.js"></script>
</head>
<body data-spy="scroll" data-target=".subnav" data-offset="50">

{if $adminInfo}
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="{$ADMIN_PATH}/">管理画面</a>
			<div class="nav-collapse">
				<ul class="nav">
					<li{if $pageData.c == 'data'} class="active"{/if}><a href="{$ADMIN_PATH}/data/user/">データ管理</a></li>
					<li{if $pageData.c == 'content'} class="active"{/if}><a href="{$ADMIN_PATH}/content/faqcategory/">コンテンツ管理</a></li>
					<li{if $pageData.c == 'env'} class="active"{/if}><a href="{$ADMIN_PATH}/env/">環境設定</a></li>
				</ul>
				<p class="navbar-text pull-right"><a href="#">ログアウト</a></p>
			</div>
		</div>
	</div>
</div>
{/if}

<div class="container-fluid">
	<div class="row-fluid">
{if $adminInfo}
		<div class="span2">
			<div class="well sidebar-nav">
			{if $pageData.c == 'data'}
				<ul class="nav nav-list">
					<li class="nav-header">データ管理</li>
					<li{if $pageData.n == 'user'} class="active"{/if}><a href="{$ADMIN_PATH}/data/user/">ユーザー管理</a></li>
					<li{if $pageData.n == 'advice'} class="active"{/if}><a href="{$ADMIN_PATH}/data/advice/">すべての相談窓口</a></li>
					<li{if $pageData.n == 'examine'} class="active"{/if}><a href="{$ADMIN_PATH}/data/advice/examine">承認待ち相談窓口</a></li>
				</ul>
			{else if $pageData.c == 'content'}
				<ul class="nav nav-list">
					<li class="nav-header">コンテンツ管理</li>
					<li{if $pageData.n == 'faqcategory'} class="active"{/if}><a href="{$ADMIN_PATH}/content/faqcategory/">よくある質問カテゴリー</a></li>
					<li{if $pageData.n == 'faq'} class="active"{/if}><a href="{$ADMIN_PATH}/content/faq/">よくある質問</a></li>
				</ul>
			{else if $pageData.c == 'env'}
				<ul class="nav nav-list">
					<li class="nav-header">環境設定</li>
					<li><a href="#">ロゴ変更</a></li>
					<li><a href="#">外部への公開</a></li>
					<li{if $pageData.n == 'analyze'} class="active"{/if}><a href="{$ADMIN_PATH}/env/analyze">アクセス解析タグ</a></li>
					<li{if $pageData.n == 'ssl'} class="active"{/if}><a href="{$ADMIN_PATH}/env/ssl">SSL接続</a></li>
				</ul>
			{/if}
			</div>
		</div>
		<div class="span10">
{include file="$page_template"}
		</div>
{else}
		<div class="span12">
{include file="$page_template"}
		</div>
{/if}
	</div>

	<hr>

	<footer>
		<p>&copy; 2011 Esora Inc.</p>
	</footer>
</div>

<!--
{if $hiddenFrame!=1}
<h1>{$smarty.const.APP_CONST_SITE_TITLE_S} - <a href="/admin/">管理ツール</a></h1>
{/if}
<div id="head_line"></div>
<table id="main_table" border="0" width="100%"><tbody><tr>
{if $hiddenFrame!=1}
<td width="180" style="border-right:2px solid #ebeff9;vertical-align:top;">
{if $adminInfo}
{include file='admin/menu.tpl'}
{/if}
</td>
{/if}
<td valign="top">

{include file="$page_template"}

</td>
</tr></tbody></table>
-->

</body>
</html>
