<?php
//error_reporting(E_ALL & ~E_NOTICE);
//// PHP 5.3.0対応
//if (error_reporting() > 6143) {
//	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
//}
define('APP_CONST_SITE_TITLE', 'Adviner [アドバイナー] 実名で相談してアドバイスをもらうQ&Aサービス');
define('APP_CONST_SITE_TITLE2', ' Adviner [アドバイナー]');
define('APP_CONST_SITE_TITLE3', ' | Adviner [アドバイナー]');
define('APP_CONST_SITE_TITLE4', ' | 相談とアドバイスで繋がるQ&Aサービス Adviner [アドバイナー]');
define('APP_CONST_SITE_TITLE_TOP', 'Adviner 実名で相談してアドバイスをもらうQ&Aサービス');
define('APP_CONST_SITE_SHARE_MSG', '相談とアドバイスで繋がる Adviner [アドバイナー] をはじめました');
define('APP_CONST_SITE_TITLE_F', 'Adviner [アドバイナー]');
define('APP_CONST_SITE_TITLE_J', 'アドバイナー');
define('APP_CONST_SITE_TITLE_S', 'Adviner');
define('APP_CONST_BIZ_NAME', 'Adviner運営事務局');
define('APP_CONST_SITE_DOMAIN', 'adviner.com');
define('APP_CONST_META_KEYWORDS', '相談,アドバイス,質問,悩み,問題解決,フェイスブック,facebook');
define('APP_CONST_META_DESCRIPTION', 'Adviner（アドバイナー）はFacebookアカウントによる実名で質問、相談してアドバイスをもらうQ&Aサービス。無料相談・有料相談とアドバイスが一対一でやり取りされるため、質の高い回答で悩みや問題を解決します。');

define('APP_CONST_MAIN_FRAME', '_frame/main_frame');
define('APP_CONST_BLANK_FRAME', '_frame/blank_frame');
define('APP_CONST_ADMIN_FRAME', '_frame/admin_frame');
define('APP_CONST_EMPTY_FRAME', '_frame/empty_frame');
define('APP_CONST_POPUP_FRAME', '_frame/popup_frame');
define('APP_CONST_NOTFOUND_FRAME', '_frame/notfound_frame');

define('APP_CONST_PROTOCOL', empty($_SERVER['HTTPS']) ? 'http' : 'https');
define('APP_CONST_JS_VER', '1202270001');
define('APP_CONST_CSS_VER', '1202270001');
define('APP_CONST_JS_URL', '');
define('APP_CONST_CSS_URL', '');
define('APP_CONST_IMG_URL', '');
define('APP_CONST_JS_PATH', '/js/');
define('APP_CONST_CSS_PATH', '/css/');
define('APP_CONST_IMG_PATH', '/img/');
define('APP_CONST_TMP_DIR', APP_DIR . '/tmp');
define('APP_CONST_PAGE_LIMIT', 20);
define('APP_CONST_TMP_DIR_REMOVE_DAYS', 7);

if (APP_ENV == 'release') {
	define('APP_CONST_INFO_EMAIL', 'support@adviner.com');
	define('APP_CONST_SERVICE_EMAIL', 'support@adviner.com');
	define('APP_CONST_CONTACT_EMAIL', 'support@adviner.com');
} else {
	define('APP_CONST_INFO_EMAIL', 'support@adviner.com');
	define('APP_CONST_SERVICE_EMAIL', 'support@adviner.com');
	define('APP_CONST_CONTACT_EMAIL', 'support@adviner.com');
}
define('APP_CONST_CONTACT_TO_EMAIL', 'support@adviner.com');

define('APP_CONST_REGIST_FIRST_TIME', 86400);
define('APP_CONST_UNIQ_ADVICE_COOKIE_NAME', '_uniq_advice');

/** 認証 */
define('APP_CONST_USER_AUTH_NAME', 'userInfo');
define('APP_CONST_USER_AUTH_TIME', 86400);
//define('APP_CONST_USER_AUTH_TIME', 3600);
define('APP_CONST_USER_LOGIN_FIRST_PAGE', '/');
// 認証の保存期間は2週間
define('APP_CONST_USER_REMEMBER_DAY', 14);
// 有料アドバイス
define('APP_CONST_USER_DEFAULT_CHARGE_RATE', 70);
define('APP_CONST_USER_DEFAULT_PAYMENT_MIN', 3000);

define('APP_CONST_ADMIN_PATH', '/'.APP_ADMIN_DIR);
define('APP_CONST_ADMIN_AUTH_NAME', 'adminInfo');
define('APP_CONST_ADMIN_AUTH_TIME', 86400);
define('APP_CONST_ADMIN_AUTH_FILE', APP_DIR.'/libs/.authfile');
define('APP_CONST_ADMIN_PAGE_LIMIT', 100);

define('APP_CONST_SECURITY_CODE_NAME', 'security_code');
define('APP_CONST_SECURITY_TOKEN_NAME', 'security_token');

//define('APP_CONST_TWITTER_OAUTH_CONSUMER_KEY', '7px0e0VWOhBLIMjNDRkww');
//define('APP_CONST_TWITTER_OAUTH_CONSUMER_SECRET', 'g2a6yX0d6bc5SVNXwYC7V4Ny2F3g2eIkW3g5MFZw4');
//define('APP_CONST_TWITTER_OAUTH_CALLBACK_PATH', 'twitter/signin');

define('APP_CONST_FACEBOOK_OAUTH_BUTTON_TEXT', 'Login with Facebook');
//if (APP_ENV == 'release') {
	define('APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY', '141936772550911');
	define('APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET', 'd1bbb153fb6b664cf4d68f039e9fea02');
//} else {
//	define('APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY', '214977381884698');
//	define('APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET', '516f95630a78eba6be5c5170458f1f8d');
//}
define('APP_CONST_FACEBOOK_OAUTH_CALLBACK_PATH', 'sessions/facebook/signin');
define('APP_CONST_FACEBOOK_OAUTH_CODE_NAME', 'facebook_oauth_code');
define('APP_CONST_FACEBOOK_OAUTH_TOKEN_NAME', 'facebook_oauth_access_token');
define('APP_CONST_FACEBOOK_USER_ID', '100001188508280');
define('APP_CONST_FACEBOOK_DESCRIPTION', 'Facebookアカウントによる実名で質問、相談してアドバイスをもらうQ&Aサービスです。');
//
//define('APP_CONST_MIXI_DEV_KEY', '483d62db980d90653000b22e6f55689539641240');
//
//define('APP_CONST_OPENID_STORE_DIR', APP_CONST_TMP_DIR);
//define('APP_CONST_OPENID_MIXI', 'https://mixi.jp/');
//define('APP_CONST_OPENID_MIXI_CRT', APP_DIR.'/libs/crt/mixi.jp.crt');
//define('APP_CONST_OPENID_CALLBACK_PATH', 'openid/signin');
//define('APP_CONST_OPENID_TRUST_DIR', 'openid/');

class AppConst
{
	public static $mainCategorys = array(
		1 => '子育て・学校',
		2 => 'キャリア・仕事',
		3 => '料理・グルメ',
		4 => '恋愛・人間関係の悩み',
		5 => '暮らし',
		6 => '知識・教養・学問',
		7 => '経済・お金',
		8 => '美容・健康',
		9 => '趣味・エンターテイメント',
		10 => 'ファッション',
		11 => 'インターネット・パソコン',
		12 => '地域情報・旅行',
		13 => 'ニュース・時事'
	);

	public static $evaluateType = array(
		1 => '☆（悪かった）',
		2 => '☆☆（ピンとこなかった）',
		3 => '☆☆☆（普通）',
		4 => '☆☆☆☆（良かった）',
		5 => '☆☆☆☆☆（とても参考になった）'
	);

	public static $evaluatePoint = array(
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5
	);

	public static $adviceFrequency = array(
		1 => 'できる限りすべてに回答します',
		2 => '1日に数人程度は回答できます',
		3 => '2～3日に数人程度は回答できます',
		4 => '4～5日に数人程度は回答できます',
		5 => '1週間に数人程度は回答できます',
		6 => '週末や休日にまとめて回答します'
	);

	/**
	 * 選択用
	 * @var array
	 */
	public static $adviceCondition = array(
		1 => 'Twitterで評価コメントをツイートしてほしい',
		2 => 'Facebookで評価コメントをシェアしてほしい',
		3 => 'ブログやSNS等でアドバイザー（あなた）を紹介してほしい'
	);

	/**
	 * 表示用
	 * @var array
	 */
	public static $adviceConditionShow = array(
		1 => 'Twitterで評価コメントをツイートしてほしい',
		2 => 'Facebookで評価コメントをシェアしてほしい',
		3 => 'ブログやSNS等でアドバイザーを紹介してほしい'
	);

	public static $searchTypes = array(
		1 => '相談窓口を検索する',
		2 => 'アドバイスを検索する',
		3 => '相談内容を検索する'
	);

	public static $depositTypes = array(
		1 => '普通',
		2 => '当座'
	);

	public static $adviceChargeCount = array(
		0 => '無制限',
		1 => '1回',
		2 => '2回',
		3 => '3回',
		4 => '4回',
		5 => '5回',
		6 => '6回',
		7 => '7回',
		8 => '8回',
		9 => '9回',
		10 => '10回',
	);

	/**
	 * PICKUP用
	 * @var array
	 */
	public static $pickupAdviceIds = array(
		73,
		6,
		149,
		28,
		33,
		15
	);

	// 送信用メールサーバー
//	public static $utilSmtpParams = array(
//		'host' => 'mail39.heteml.jp',
//		'port' => '587',
//		'auth' => true,
//		'username' => 'info@adviner.com',
//		'password' => 'smsx5vg0w5ulcjv2',
//	);
	public static $utilSmtpParams = array(
		'host' => 'smtp.gmail.com',
		'port' => '587',
		'auth' => true,
		'username' => 'support@adviner.com',
		'password' => 'ewebnju2q2qqixjh'
	);

	public static $notIp = array(
//		'^127\.0\.0\.1$',
		'^49\.135\.66\.[0-9]+$',
		'^49\.135\.202\.[0-9]+$',
		'^49\.135\.62\.[0-9]+$'
	);

	public static $notUserAgent = array(
		'Googlebot',
		'$_agentname',
		'AppEngine-Google',
		'Baiduspider',
		'bingbot',
		'Butterfly',
		'ceron.jp/',
		'Crowsnest',
		'DailyPerfect/',
		'facebookexternalhit',
		'Hatena',
		'ichiro/mobile',
		'JS-Kit',
		'libwww-perl',
		'livedoor ScreenShot',
		'LinkedInBot/',
		'Mediapartners-Google',
		'MetaURI',
		'mixi-check',
		'NHN Corp.',
		'NING',
		'NjuiceBot',
		'OneRiot',
		'PostRank',
		'PycURL',
		'Python-urllib',
		'ScribdReader/',
		'Summify',
		'RockMelt',
		'TweetmemeBot',
		'Twib::Crawler',
		'Twitterbot',
		'TwitterIrcGateway',
		'Twitturly',
		'UnwindFetchor',
		'Voyager',
		'WWW::Document',
		'Y!J-AGENT',
		'Yahoo! Slurp',
		'Yeti/'
	);
}

function app_split_to_array($str)
{
	if (empty($str)) return array();
	if (substr($str, 0, 1) == '[') $str = substr($str, 1);
	if (substr($str, -1) == ']') $str = substr($str, 0, strlen($str)-1);
	return explode('][', $str);
}
function app_is_notip()
{
	$ip = trim($_SERVER['REMOTE_ADDR']);
	$notip = AppConst::$notIp;
	if (empty($ip) || empty($notip)) return false;
	foreach ($notip as $not) {
		if (preg_match('/'.$not.'/', $ip)) return true;
	}
	return false;
}
function app_create_link($str)
{
	return preg_replace('/(https?:\/\/[a-z0-9_=%#\-\/\.\?\&\~\!;]+)/im', '<a href="$1" target="_blank">$1</a>', $str);
}
function app_get_user_charge_rate(&$user)
{
	return empty($user['charge_rate']) ? APP_CONST_USER_DEFAULT_CHARGE_RATE : $user['charge_rate'];
}
?>
