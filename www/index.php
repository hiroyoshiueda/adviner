<?php
if (strpos(__FILE__, '/apps/adviner.com') !== false) {
	// 本番
	define('APP_DIR', '/apps/adviner.com');
	define('APP_WWW_DIR', '/apps/adviner.com/www');
	define('APP_CONF', APP_DIR . '/conf/app.conf');
	define('APP_ENV', 'release');
	// APCキャッシュを無効にする場合はfalse
	define('APP_APC_CACHE', true);
	// GAアカウント
	define('APP_GA_ACOUNT', 'UA-25185515-1');
	// コンテンツを非公開にする場合はfalse
	define('APP_IS_PUBLIC', true);
	// admin接続にhttpも許可する場合はfalse
	define('APP_ADMIN_SSL', true);
	define('APP_ADMIN_DIR', 'ad-admin');
} else if (strpos(__FILE__, '/apps/stg.adviner.com') !== false) {
	// 開発本番
	define('APP_DIR', '/apps/stg.adviner.com');
	define('APP_WWW_DIR', '/apps/stg.adviner.com/www');
	define('APP_CONF', APP_DIR . '/conf/app_stg.conf');
	define('APP_ENV', 'release');
	define('APP_APC_CACHE', false);
	define('APP_GA_ACOUNT', '');
	define('APP_IS_PUBLIC', false);
	define('APP_ADMIN_SSL', true);
	define('APP_ADMIN_DIR', 'ad-admin');
} else {
	// 開発
	define('APP_DIR', '/Users/Hiroyoshi/Documents/workspace/adviner.com');
	define('APP_WWW_DIR', '/Users/Hiroyoshi/Documents/workspace/adviner.com/www');
	define('APP_CONF', APP_DIR . '/conf/app_dev.conf');
	define('APP_ENV', 'dev');
	define('APP_APC_CACHE', true);
	define('APP_GA_ACOUNT', 'UA-25185515-2');
	define('APP_IS_PUBLIC', true);
	define('APP_ADMIN_SSL', true);
	define('APP_ADMIN_DIR', 'ad-admin');
}
require APP_DIR . '/simplity/Simplity.php';
require APP_DIR . '/libs/AppConst.php';
Sp::init();
Sp::execute();
?>