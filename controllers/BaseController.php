<?php
Sp::import('CategorysDao', 'dao');
/**
 * ベースコントローラー
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class BaseController extends SpController
{
	/**
	 * ご指定のURLが間違っています。
	 */
	const ERROR_PAGE_MESSAGE1 = "ご指定のURLが間違っています。";

	/**
	 * ご指定のデータは閲覧できません。
	 */
	const ERROR_PAGE_MESSAGE2 = "ご指定のデータは閲覧できません。";

	/**
	 * ご指定のページは閲覧できません。
	 */
	const ERROR_PAGE_MESSAGE3 = "ご指定のページは閲覧できません。";

	/**
	 * ログインする必要があります。
	 */
	const ERROR_PAGE_MESSAGE4 = "ログインする必要があります。";

	/**
	 * このデータは既に送信済みです。重複して送信することはできません。
	 */
	const ERROR_PAGE_MESSAGE5 = "このデータは既に送信済みです。重複して送信することはできません。";

	/**
	 * 処理中にシステムエラーが発生しました。しばらくしてから再度お試しください。
	 */
	const ERROR_PAGE_MESSAGE6 = "処理中にシステムエラーが発生しました。しばらくしてから再度お試しください。";

	/**
	 * 処理中にシステムエラーが発生しました。画面を更新してから再度実行してください。
	 */
	const ERROR_AJAX_MESSAGE1 = '処理中にシステムエラーが発生しました。画面を更新してから再度実行してください。';

	/**
	 * 入力エラーがあります。
	 */
	const ERROR_AJAX_INPUT_MESSAGE = '入力エラーがあります。';

	/**
	 * カテゴリーキャッシュ名
	 */
	const CACHE_STORE_CATEGORY_SET = 'cache_store_category_set';

	/**
	 * キャッシュを無効にする場合はfalse
	 */
	const IS_LOAD_USER_RANK = true;
	const IS_LOAD_FOLLOW_DATA = false;

	const CACHE_FRIENDS_ACTIVE_LIST = 'cache_friends_active_list';

	private $gnavi_titles = array();
	private $gnavi_links = array();

	/**
	 * メイン実行前の共通処理
	 */
	public function preExecute()
	{
		// テンプレート初期設定
		$this->form->setTemplateDir(APP_DIR.'/templates');
		$this->form->setCompileDir(APP_DIR.'/templates_c');
		$this->form->setSmartyPlugins(APP_DIR . constant('app_smarty_plugins_dir'));

		// AppConst値をSp変数として登録
		$appconst = get_class_vars('AppConst');
		$this->form->setSp('AppConst', $appconst);

		$userInfo = $this->getUserInfo();

		$this->form->setSp('userInfo', $userInfo);
		$this->form->setSp('sideProfile', $userInfo);
		$this->form->setSp('keywords', APP_CONST_META_KEYWORDS);
		$this->form->setSp('description', APP_CONST_META_DESCRIPTION);
		$this->form->setSp('og_description', APP_CONST_FACEBOOK_DESCRIPTION);
		$this->form->setSp('JS_URL', constant('APP_CONST_JS_URL'));
		$this->form->setSp('CSS_URL', constant('APP_CONST_CSS_URL'));
		$this->form->setSp('IMG_URL', constant('APP_CONST_IMG_URL'));
		$this->form->setSp('HTTP_URL', constant('app_site_url'));
		$this->form->setSp('HTTPS_URL', constant('app_site_ssl_url'));
		$this->form->setSp('REAL_URL', constant('app_site_real_url'));
		$this->form->setSp('PROTOCOL', constant('APP_CONST_PROTOCOL'));
		$this->form->setSp('USER_CHARGE_RATE', $this->getUserChargeRate());

		$this->setSecurityHeader();

		// API呼出しはここで終了
		if (substr($this->form->get('env_page_path'),0,4) == 'api/') {
			return true;
		}

//		if ($this->checkUserAuth())
//		{
//			$friends_list = $this->form->get(self::CACHE_FRIENDS_ACTIVE_LIST);
//			$this->logger->debug($friends_list);
//			if (isset($friends_list['expires']) && $friends_list['expires'] > time())
//			{
//				$this->form->set('friends_user_list', $friends_list['friends_user_list']);
//				$this->form->set('friends_advice_list', $friends_list['friends_advice_list']);
//				$this->form->set('is_friends_list', '1');
//			}
//		}

//		$this->form->set('is_friends_list', '0');
		$this->form->setDefault('qtype', '1');
		$this->form->setDefault('qopt', 'default');

		$category_set = $this->getLoadCategorySet(new CategorysDao($this->db));
		$this->form->set('category_set', $category_set);
		$this->setCategoryList($category_set);

//		// home/ 以下は認証領域
//		if (substr($this->form->get('env_page_path'),0,5) == 'home/') {
//			if ($this->checkUserAuth()===false) {
//				$this->loginPage();
//				return false;
//			}
//		}

//		// https -> http
//		$path = strtolower($_SERVER['REQUEST_URI']);
//		if (Util::startsWith($path, '/user/mypage/setting/') === false && Util::startsWith($path, '/user/signup/') === false) {
//			if ($this->forceHttp()) return false;
//		}

//		$this->resp->setHeader('Content-Language', 'ja');

//		$lastModified = gmdate('D, d M Y H:i:s T', time() - 4000);
//		$this->resp->setHeader('Last-Modified', $lastModified);
//		$etag = '"'.md5($lastModified).'"';
//		$this->resp->setHeader('ETag', $etag);

		return true;
	}

	/**
	 * メイン実行後の共通処理
	 */
	public function postExecute()
	{
		$subtitle1 = $this->form->get('subtitle_1');
		$subtitle2 = $this->form->get('subtitle_2');

		$title = $subtitle1;
		if ($subtitle2 != '') $title .= ' - '.$subtitle2;
//		$title .= $subtitle2;
//		if ($subtitle2 != '') $title .= ' | ';
		if ($title == '') {
			$title = $this->form->get('maintitle', APP_CONST_SITE_TITLE);
		} else {
			$title .= $this->form->get('maintitle', APP_CONST_SITE_TITLE4);
		}
		$this->form->setTitle($title);

		if (count($this->gnavi_titles)>0)
		{
			$this->form->setSp('gnavi_titles', $this->gnavi_titles);
			$this->form->setSp('gnavi_links', $this->gnavi_links);
		}

		return true;
	}

	/**
	 * デフォルトエントリポイント
	 */
	public function index()
	{
		return $this->forward('index');
	}

	/**
	 * ページが存在しない場合
	 */
	public function notfound()
	{
		return $this->resp->setStatus(404);
	}

	/**
	 * 画面設定
	 */
	protected function forward($forward, $frame=null)
	{
		$forward = array($forward);
		if ($frame!==null) $forward[] = $frame;
		else $forward[] = APP_CONST_MAIN_FRAME;
		return $this->form->forward($forward);
	}

	/**
	 * タイトル設定
	 */
	protected function setTitle($subtitle1, $subtitle2='', $title=null)
	{
		$this->form->set('subtitle_1', $subtitle1);
		$this->form->set('subtitle_2', $subtitle2);
		if ($title!==null) $this->form->set('maintitle', $title);
	}

	protected function setKeywords($keywords, $type='before')
	{
		$this->form->setSp('keywords', $keywords.$this->form->getSp('keywords'));
	}

	protected function setDescription($description, $type='')
	{
		$description = preg_replace('/[　\s\r\n\t]+/u', '', $description);
		$description = mb_substr($description, 0, 200);

		$this->form->setSp('description', $description);
		$this->form->setSp('og_description', $description);
	}

	/**
	 * エラー画面の呼び出し
	 */
	protected function errorPage($msg='', $frame=null)
	{
		if ($msg!='') $this->form->set('message', $msg);
		return $this->forward('error', $frame);
	}

	protected function notfoundPage()
	{
		$this->resp->setStatus(404);
		return $this->forward('notfound', APP_CONST_NOTFOUND_FRAME);
	}

	/**
	 * ログイン画面の呼び出し
	 */
	protected function loginPage()
	{
//		$this->deleteUserInfo();

		$this->createSecurityCode();

		$this->form->setDefault('loc', $this->form->getPageUrl());
		$this->form->setParameterForm('loc');
		$this->form->setParameterForm('_hash');

//		$this->setTitle('ログイン');

		return $this->forward('login', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 認証チェック
	 */
	protected function checkUserAuth()
	{
		$c = $this->getUserInfo();
		if (empty($c)) {
			return false;
		}
		return true;
	}

	/**
	 * 認証後情報の登録
	 */
	protected function setUserInfo($user_info)
	{
		if (is_array($user_info) === false || empty($user_info)) return;
		$user_info['id'] = (int)$user_info['user_id'];
		$user_info['ts'] = time();
		$user_info['date'] = date('Y-m-d H:i:s', $user_info['ts']);
		$user_info['remote'] = $_SERVER['REMOTE_ADDR'];
		$user_info['uagent'] = $_SERVER['HTTP_USER_AGENT'];
		unset($user_info['password']);
		$this->form->setSession(APP_CONST_USER_AUTH_NAME, $user_info);
		return;
	}

	/**
	 * 認証情報の上書き
	 * @param array $new_info
	 */
	protected function updateUserInfo($new_info)
	{
		$user_info = $this->getUserInfo();
		foreach ($user_info as $key => $val) {
			if (isset($new_info[$key])) {
				$user_info[$key] = $new_info[$key];
			}
		}
		$this->form->setSp('userInfo', $user_info);
		$this->form->setSession(APP_CONST_USER_AUTH_NAME, $user_info);
		return;
	}

	/**
	 * 認証情報の取得
	 */
	protected function getUserInfo()
	{
		return $this->form->get(APP_CONST_USER_AUTH_NAME);
	}

	/**
	 * 認証情報の削除
	 */
	protected function deleteUserInfo()
	{
		//session_unset();
		$this->form->setSp('userInfo', null);
		return $this->form->clearSession(APP_CONST_USER_AUTH_NAME);
	}

	/**
	 * 添付ファイルの一時ファイル保存
	 * @param string $key
	 * @return boolean
	 */
	protected function copyFileTemp($key, $tmp_dir, $tmp_ext='', $prefix='', $name_type=0)
	{
		$ret = true;
		$k = $key.'_file';
		@mkdir($tmp_dir, 0755);

		if (isset($_FILES[$k]) && $_FILES[$k]['name']!='') {
			$name = SpFilter::sanitize($_FILES[$k]['name']);
			$ext = Util::getExtension($name);
			$size = 0;
			if ($name_type == 0) {
				$tmpfile = uniqid($prefix, true).'.'.$ext.$tmp_ext;
			} else if ($name_type == 1) {
				$tmpfile = $prefix.'.'.$ext.$tmp_ext;
			}
			if (move_uploaded_file($_FILES[$k]['tmp_name'], $tmp_dir.'/'.$tmpfile)) {
				$size = filesize($tmp_dir.'/'.$tmpfile);
			} else {
				$ret = false;
				$this->logger->error('コピーに失敗。'.$_FILES[$k]['tmp_name'].' > '.$tmp_dir.'/'.$tmpfile);
				$this->form->setValidateErrors($k, 'ファイルのコピーに失敗');
			}
			$this->form->set($key.'_file', $name);
			$this->form->set($key.'_path', $tmpfile);
			$this->form->set($key.'_size', $size);
		}

		return $ret;
	}

	/**
	 * ユニークカウント用
	 * @param int $id
	 * @param string $cookie_name
	 * @param string [ $path = '/' ]
	 * @return boolean true：未カウント、false：カウント済
	 */
	protected function setUniqCount($id, $cookie_name, $path='/')
	{
		$key_id = sprintf("/%d/", $id);
		$cookie_value = '';
		if (isset($_COOKIE[$cookie_name])) {
			$cookie_value = trim($_COOKIE[$cookie_name]);
			if (strpos($cookie_value, $key_id) !== false) return false;
		}
		$cookie_value .= $key_id;
		$today_ts = mktime(0,0,0,date('n'),date('j'),date('Y'));
		// 本日の23:59:59まで有効
		$expire = $today_ts + 86399;
		return setcookie($cookie_name, $cookie_value, $expire, $path);
	}

	protected function getUniqStatus($id, $cookie_name)
	{
		$key_id = sprintf("/%d/", $id);
		$cookie_value = '';
		if (isset($_COOKIE[$cookie_name])) {
			$cookie_value = trim($_COOKIE[$cookie_name]);
			if (strpos($cookie_value, $key_id) !== false) return false;
		}
		return true;
	}

	/**
	 * 除外IPをチェック
	 * @return true：除外IP、false：除外IPではない
	 */
	protected function isNotIp()
	{
		return app_is_notip();
	}

	/**
	 * 除外ユーザエージェントをチェック
	 * @return true：除外UA、false：除外UAではない
	 */
	protected function isNotUserAgent()
	{
		$agent = trim($_SERVER['HTTP_USER_AGENT']);
		if (empty($agent)) return true;
		foreach (AppConst::$notUserAgent as $key) {
			if (strpos($agent, $key) !== false) return true;
		}
		return false;
	}

	/**
	 * 2重投稿禁止用トークンの生成
	 */
	protected function createSecurityCode()
	{
		$this->form->setSession(APP_CONST_SECURITY_CODE_NAME, md5(uniqid('', true)));
		$this->form->setParameter(APP_CONST_SECURITY_TOKEN_NAME, $this->form->get(APP_CONST_SECURITY_CODE_NAME));
		return $this->form->get(APP_CONST_SECURITY_CODE_NAME);
	}

	/**
	 * 2重投稿禁止用トークンのチェック
	 */
	protected function checkSecurityCode()
	{
		$token = $this->form->get(APP_CONST_SECURITY_TOKEN_NAME);
		$code = $this->form->get(APP_CONST_SECURITY_CODE_NAME);
		$this->form->clearSession(APP_CONST_SECURITY_CODE_NAME);
		if ($token != '' && $code != '' && $token == $code) {
			return true;
		} else {
			return false;
		}
	}

	protected function setSocialButton($target=array('fb_like', 'twitter', 'g_plusone'))
	{
		//if (in_array('twitter', $target)) $this->form->setFooterScript(constant('APP_CONST_PROTOCOL').'://platform.twitter.com/widgets.js');
		if (in_array('g_plusone', $target)) $this->form->setFooterScript(constant('APP_CONST_PROTOCOL').'://apis.google.com/js/plusone.js', '{"lang":"ja"}');
		if (empty($_SERVER['HTTPS']))
		{
			if (in_array('hatena', $target)) $this->form->setFooterScript('http://b.st-hatena.com/js/bookmark_button.js');
			if (in_array('fb_share', $target)) $this->form->setFooterScript('http://static.ak.fbcdn.net/connect.php/js/FB.Share');
		}
	}

	protected function isForceHTTPS()
	{
		if (isset($_SERVER['HTTPS']) === false || empty($_SERVER['HTTPS'])) {
			return true;
		}
		return false;
	}

	protected function forceHTTPS()
	{
		return $this->resp->sendRedirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	}

	/**
	 * userRankを10分間隔で更新
	 * @param UserRanksDao $userRanksDao
	 */
	protected function setLoadUserRank(&$userRanksDao)
	{
		if ($this->checkUserAuth())
		{
			$userInfo = $this->getUserInfo();
			$userRank = $this->form->get('userRank');
			$ts = time();
			if (empty($userRank) === false && $userRank['ts'] > $ts && self::IS_LOAD_USER_RANK)
			{
				$this->form->setSp('userRank', $userRank);
				return $userRank;
			}
			else
			{
				$userRank = $userRanksDao->getItem($userInfo['id']);
				$userRank['ts'] = $ts + 180;
				$this->form->setSp('userRank', $userRank);
				$this->form->setSession('userRank', $userRank);
				return $userRank;
			}
		}
		return null;
	}

	/**
	 * followDataを15分間隔で更新
	 * @param FollowsDao $followsDao
	 * @param AdvicesDao $advicesDao
	 */
	protected function getLoadFollowData(&$followsDao, &$advicesDao)
	{
		if ($this->checkUserAuth())
		{
			$userInfo = $this->getUserInfo();
			$followData = $this->form->get('followData');
			$ts = time();
			if (empty($followData) === false && $followData['ts'] > $ts && self::IS_LOAD_FOLLOW_DATA)
			{
				unset($followData['ts']);
				$this->form->setSp('followData', $followData);
				return $followData;
			}
			else
			{
				$follow_list = $followsDao->getList($userInfo['id']);
				if (empty($follow_list)) {
					$this->form->setSp('followData', null);
					$this->form->setSession('followData', null);
					return null;
				}
				$followData = array('user_id'=>array(), 'advice_id'=>array(), 'ts'=>0);
				foreach ($follow_list as $d)
				{
					if ($d['follow_user_id'] > 0)
					{
						$followData['user_id'][] = $d['follow_user_id'];
					}
					else if ($d['follow_advice_id'] > 0)
					{
						$followData['advice_id'][] = $d['follow_advice_id'];
					}
					else if ($d['follow_category_id'] > 0)
					{
						$advicesDao->reset();
						$list = $advicesDao->getIdListByCategoryId($d['follow_category_id']);
						if (count($list) > 0)
						{
							foreach ($list as $dd) {
								if ($dd['advice_id'] > 0) $followData['advice_id'][] = $dd['advice_id'];
							}
						}
					}
				}
				$followData['user_id'] = array_unique($followData['user_id']);
				$followData['advice_id'] = array_unique($followData['advice_id']);
				$followData['ts'] = $ts + (60 * 15);
				$this->form->setSp('followData', $followData);
				$this->form->setSession('followData', $followData);
				unset($followData['ts']);
				return $followData;
			}
		}
		return null;
	}

	/**
	 * 共有メモリにキャッシュしたカテゴリー情報を取得
	 * @param CategorysDao $categorysDao
	 */
	protected function getLoadCategorySet(&$categorysDao)
	{
		$is_apc = function_exists('apc_store');
		$category_set = null;
		if ($is_apc && APP_APC_CACHE)
		{
			$category_set = apc_fetch(self::CACHE_STORE_CATEGORY_SET);
		}
		if (empty($category_set))
		{
			$category_set = $categorysDao->getKeySet();
			if ($is_apc && APP_APC_CACHE) apc_store(self::CACHE_STORE_CATEGORY_SET, $category_set, 180);
		}
		return $category_set;
	}

	protected function clearLoadCategorySet()
	{
		@apc_delete(self::CACHE_STORE_CATEGORY_SET);
	}

	protected function setCategoryList(&$category_set)
	{
		// カテゴリー
		$category_list = array();
		foreach ($category_set as $cid => $d)
		{
			if ($d['total']>0) {
				$main_category_id = $d['main_category_id'];
				if (isset($category_list[$main_category_id])) {
					$category_list[$main_category_id]['total'] += $d['total'];
				} else {
					$category_list[$main_category_id] = array(
						'main_category_id' => $main_category_id,
						'main_cname' => AppConst::$mainCategorys[$main_category_id],
						'total' => (int)$d['total']
					);
				}
			}
		}
		$this->form->set('category_list', $category_list);
	}

	protected function jsonPage(&$json_data, $security_token=true)
	{
		if ($security_token) $json_data['security_token'] = $this->createSecurityCode();

//		$this->form->set('data', Util::jsonEncode(array('lists'=>$json_data)));
		if (preg_match("/^5\.3/", PHP_VERSION)) {
			$this->form->set('data', json_encode(array('lists'=>$json_data), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
		} else {
			$this->form->set('data', json_encode(array('lists'=>$json_data)));
		}

		$this->resp->setContentType(SpResponse::CTYPE_JSON);
//		$this->resp->setHeader('X-Content-Type-Options', 'nosniff');

		return $this->forward('json', APP_CONST_EMPTY_FRAME);
	}

	protected function checkXHR()
	{
		return ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
	}

//	/**
//	 * サイドに友達リストを読み込む
//	 */
//	protected function setSideFriendsList()
//	{
//		if ($this->checkUserAuth())
//		{
//			$friends_list = $this->form->get(self::CACHE_FRIENDS_ACTIVE_LIST);
//			if (isset($friends_list['expires']) && $friends_list['expires'] > time())
//			{
//				//$this->form->set('friends_user_list', $friends_list['friends_user_list']);
//				//$this->form->set('friends_advice_list', $friends_list['friends_advice_list']);
//				$this->form->set('popular_list', $friends_list['popular_list']);
//				$this->form->set('is_friends_list', '0');
//			} else {
//				$this->form->set('is_friends_list', '1');
//			}
//		}
//	}

	/**
	 * サイド用新着相談窓口の読み込み
	 */
	protected function setSideRecentList()
	{
		Sp::import('AdvicesDao', 'dao', true);

		$userInfo = $this->getUserInfo();

		$AdvicesDao = new AdvicesDao($this->db);

		if ($userInfo && $userInfo['id'] > 0)
		{
			$recent_list = $AdvicesDao->getRecentNofollowList($userInfo['id'], 5);
		}
		else
		{
			$recent_list = $AdvicesDao->getNewList(5);
		}

		return $this->form->set('side_recent_list', $recent_list);
	}

	/**
	 * サイド用同一カテゴリーの他の相談窓口
	 * @param array $category_ids
	 * @param int $not_advice_id
	 */
	protected function setSidePopularListByCategory($category_ids, $not_advice_id)
	{
		Sp::import('AdvicesDao', 'dao', true);

		$userInfo = $this->getUserInfo();

		$AdvicesDao = new AdvicesDao($this->db);

		if ($not_advice_id > 0) $AdvicesDao->addWhere('a.'.AdvicesDao::COL_ADVICE_ID, $not_advice_id, '!=');

		if ($userInfo && $userInfo['id'] > 0)
		{
			$popular_list = $AdvicesDao->getPopularNofollowList($userInfo['id'], 5, $category_ids);
		}
		else
		{
			$popular_list = $AdvicesDao->getPopularList(5, $category_ids);
		}

		return $this->form->set('side_popular_list', $popular_list);
	}

//	protected function getFbOAuthUrl($redirect_uri)
//	{
//		$state = md5(uniqid(rand(), true));
//		$this->form->setSession('check_state', $state);
//		// https://www.facebook.com/dialog/oauth?client_id=YOUR_APP_ID&redirect_uri=YOUR_URL
//		$oauth_url  = 'https://www.facebook.com/dialog/oauth?client_id=' . APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY
//					. '&redirect_uri=' . urlencode($redirect_uri)
//					. '&scope=user_about_me,publish_stream,email,user_birthday,user_website'
//					. '&state=' . $state;
//		return $oauth_url;
//	}

	/**
	 * @param Facebook $facebook
	 */
	protected function getFbOAuth(&$facebook)
	{
		$p = array('scope' => 'user_about_me,publish_stream,email,user_birthday,user_website');
		return $facebook->getLoginUrl($p);
	}

	/**
	 * ログイン時のセッション管理
	 * - ログイン後は期限を変更
	 */
	protected function loginSession($lifetime)
	{
		ini_set('session.cookie_lifetime', $lifetime);
		$this->resp->sessionChangeId();
	}

	/**
	 * GOODボタンの表示
	 * @param int or array $advice_id
	 * @param int $consult_id
	 */
	protected function setGoodButton($advice_id=0, $consult_id=0)
	{
		Sp::import('GoodsDao', 'dao', true);

		$userInfo = $this->getUserInfo();

		$good_count = array();
		$user_count = array();

		$GoodsDao = new GoodsDao($this->db);

		if (is_array($advice_id)) $advice_id = array_unique($advice_id);
		if (is_array($consult_id)) $consult_id = array_unique($consult_id);

		if (empty($advice_id) === false || empty($consult_id) === false)
		{
			$GoodsDao->addSelect(GoodsDao::COL_PERMALINK);
			$GoodsDao->addSelectCount(GoodsDao::COL_USER_ID, 'total');
			if (is_array($advice_id))
			{
				$GoodsDao->addWhereIn(GoodsDao::COL_ADVICE_ID, $advice_id);
			}
			else if ($advice_id>0)
			{
				$GoodsDao->addWhere(GoodsDao::COL_ADVICE_ID, $advice_id);
			}
			if (is_array($consult_id))
			{
				$GoodsDao->addWhereIn(GoodsDao::COL_CONSULT_ID, $consult_id);
			}
			else if ($consult_id>0)
			{
				$GoodsDao->addWhere(GoodsDao::COL_CONSULT_ID, $consult_id);
			}
			$GoodsDao->addGroupBy(GoodsDao::COL_PERMALINK);
			$list = $GoodsDao->select();
			if (count($list) > 0)
			{
				foreach ($list as $d)
				{
					$good_count[$d['permalink']] = $d['total'];
				}
			}
		}

		if (isset($userInfo['id']) && $userInfo['id'] > 0)
		{
			$GoodsDao->reset();
			$GoodsDao->addSelect(GoodsDao::COL_PERMALINK);
			$GoodsDao->addWhere(GoodsDao::COL_USER_ID, $userInfo['id']);
			if (is_array($advice_id))
			{
				$GoodsDao->addWhereIn(GoodsDao::COL_ADVICE_ID, $advice_id);
			}
			else if ($advice_id>0)
			{
				$GoodsDao->addWhere(GoodsDao::COL_ADVICE_ID, $advice_id);
			}
			if (is_array($consult_id))
			{
				$GoodsDao->addWhereIn(GoodsDao::COL_CONSULT_ID, $consult_id);
			}
			else if ($consult_id>0)
			{
				$GoodsDao->addWhere(GoodsDao::COL_CONSULT_ID, $consult_id);
			}
			$list = $GoodsDao->select();
			if (count($list) > 0)
			{
				foreach ($list as $d)
				{
					$user_count[$d['permalink']] = 1;
				}
			}
		}

		$this->form->setSp('good', array(
			'good_count' => $good_count,
			'user_count' => $user_count
		));

		$this->form->setScript(constant('APP_CONST_JS_URL') . '/js/adviner_good.js', '', constant('APP_CONST_JS_VER'));
	}

	protected function getUserChargeRate()
	{
		$userInfo = $this->getUserInfo();
		return app_get_user_charge_rate($userInfo);
	}

	protected function getBodyToTitle($body)
	{
		return mb_substr(preg_replace('/[\r\n\t]+/u', '', $body), 0, 40);
	}

	protected function setGNavi($title, $link)
	{
		$this->gnavi_titles[] = $title;
		$this->gnavi_links[] = $link;
	}

	/**
	 * セキュリティ対策（beta）
	 * http://blog.monoweb.info/article/2012021823.html
	 * http://d.hatena.ne.jp/hasegawayosuke/20110107/p1
	 */
	protected function setSecurityHeader()
	{
		// 外部フレーム内表示をOFF IE8+,safari,chrome,Firefox
		$this->resp->setHeader('X-Frame-Options', 'DENY');
		// XSSフィルタを有効 IE8+,safari,chrome
		$this->resp->setHeader('X-XSS-Protection', '1; mode=block');
		// 内容からの推測をOFF IE8+
		$this->resp->setHeader('X-Content-Type-Options', 'nosniff');
		// Firefox > 外部コンテンツが読み込めない
		//$this->resp->setHeader('X-Content-Security-Policy', "allow 'self'");
	}
}
?>
