<?php
if (strpos(__FILE__, '/apps/adviner.com') !== false) {
	// 本番
	define('APP_DIR', '/apps/adviner.com');
	define('APP_WWW_DIR', '/apps/adviner.com/www');
	define('APP_CONF', APP_DIR . '/conf/app.conf');
	define('APP_ENV', 'release');
} else if (strpos(__FILE__, '/apps/stg.adviner.com') !== false) {
	// 開発本番
	define('APP_DIR', '/apps/stg.adviner.com');
	define('APP_WWW_DIR', '/apps/stg.adviner.com/www');
	define('APP_CONF', APP_DIR . '/conf/app_stg.conf');
	define('APP_ENV', 'release');
} else if (strpos(__FILE__, '/apps/alpha-leaders.adviner.com') !== false) {
	// alpha 本番
	define('APP_DIR', '/apps/alpha-leaders.adviner.com');
	define('APP_WWW_DIR', '/apps/alpha-leaders.adviner.com/www');
	define('APP_CONF', APP_DIR . '/conf/app_alpha.conf');
	define('APP_ENV', 'release');
} else {
	// 開発
	define('APP_DIR', '/Users/Hiroyoshi/Documents/workspace/adviner.com');
	define('APP_WWW_DIR', '/Users/Hiroyoshi/Documents/workspace/adviner.com/www');
	define('APP_CONF', APP_DIR . '/conf/app_dev.conf');
	define('APP_ENV', 'dev');
}
require APP_DIR . '/simplity/Simplity.php';
require APP_DIR . '/libs/AppConst.php';
Sp::init();

/**
 * @param BatchBookQueues
 */
$classname = $argv[1];

require APP_DIR.'/batch/BaseBatch.php';
require APP_DIR.'/batch/'.$classname.'.php';
Sp::executeBatch($classname, $argv);
?>
