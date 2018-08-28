<?php
/**
 * 管理画面用ベースコントローラー
 */
class AdminBaseController extends SpController
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
	 * 処理中にシステムエラーが発生しました。画面を更新してから再度実行してください。
	 */
	const ERROR_AJAX_MESSAGE1 = '処理中にシステムエラーが発生しました。画面を更新してから再度実行してください。';

	/**
	 * 入力エラーがあります。
	 */
	const ERROR_AJAX_INPUT_MESSAGE = '入力エラーがあります。';

	/**
	 * メイン実行前の共通処理
	 */
	public function preExecute()
	{
		// テンプレート初期設定
		$this->form->setTemplateDir(APP_DIR.'/templates');
		$this->form->setCompileDir(APP_DIR.'/templates_c');
		$this->form->setSmartyPlugins(APP_DIR . constant('app_smarty_plugins_dir'));

		if (APP_ADMIN_SSL && empty($_SERVER['HTTPS'])) {
			$this->resp->setStatus(SpResponse::SC_NOT_FOUND);
			return false;
		}

		// AppConst値をSp変数として登録
		$appconst = get_class_vars('AppConst');
		$this->form->setSp('AppConst', $appconst);

		$this->form->setSp('ADMIN_PATH', '/'.APP_ADMIN_DIR);
		$this->form->setSp('JS_URL', '');
		$this->form->setSp('CSS_URL', '');
		$this->form->setSp('IMG_URL', '');
		$this->form->setSp('HTTP_URL', constant('app_site_url'));
		$this->form->setSp('HTTPS_URL', constant('app_site_ssl_url'));
		$this->form->setSp('REAL_URL', constant('app_site_real_url'));
		$this->form->setSp('PROTOCOL', constant('APP_CONST_PROTOCOL'));

		if ($this->form->getPagePath() != 'admin/login') {
			if ($this->checkAdminAuth() === false) {
				$this->loginPage();
				return false;
			}
		}

		$this->form->setSp('adminInfo', $this->getAdminInfo());

		return;
	}

	/**
	 * メイン実行後の共通処理
	 */
	public function postExecute()
	{
		$subtitle = $this->form->get('subtitle');
		$subtitle2 = $this->form->get('subtitle2');

		$title = $subtitle;
		if ($subtitle != '') $title .= ' | ';
		$title .= $subtitle2;
		if ($subtitle2 != '') $title .= ' | ';
		$title .= $this->form->get('maintitle', '管理画面');
		$this->form->setTitle($title);

		return;
	}

	/**
	 * デフォルトエントリポイント
	 */
	public function index()
	{
		return $this->forward('index');
	}

	/**
	 * 画面設定
	 */
	protected function forward($forward, $frame=null)
	{
		$forward = array($forward);
		if ($frame!==null) $forward[] = $frame;
		else $forward[] = APP_CONST_ADMIN_FRAME;
		return $this->form->forward($forward);
	}

	/**
	 * タイトル設定
	 */
	protected function setTitle($subtitle, $subtitle2='', $title=null)
	{
		$this->form->set('subtitle', $subtitle);
		$this->form->set('subtitle2', $subtitle2);
		if ($title!==null) $this->form->set('maintitle', $title);
	}

	/**
	 * エラー画面の呼び出し
	 */
	protected function errorPage()
	{
		return $this->forward('error');
	}

	/**
	 * ページが存在しない場合
	 */
	public function notfound()
	{
		return $this->resp->setStatus(404);
	}

	/**
	 * 管理ログイン画面の呼び出し
	 */
	protected function loginPage()
	{
		$loc = $this->form->get('loc', $this->form->getPageUrl());
		$this->form->setParameter('loc', $loc);

		$this->form->set('htitle', 'ログイン');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/admin_login', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 管理画面認証チェック
	 */
	protected function checkAdminAuth()
	{
		$c = $this->getAdminInfo();
		if (empty($c)) {
			return false;
		}
		return true;
	}

	/**
	 * 認証後情報の登録
	 */
	protected function setAdminInfo($login)
	{
		$ts = time();
		$info = array(
			'login' => $login,
			'date' => date('Y-m-d H:i:s', $ts),
			'ts' => $ts,
			'remote' => $_SERVER['REMOTE_ADDR'],
			'uagent' => $_SERVER['HTTP_USER_AGENT']
		);
		$this->form->setSession(APP_CONST_ADMIN_AUTH_NAME, $info);
	}

	/**
	 * 認証情報の取得
	 */
	protected function getAdminInfo()
	{
		return $this->form->get(APP_CONST_ADMIN_AUTH_NAME);
	}

	/**
	 * 認証情報の削除
	 */
	protected function deleteAdminInfo()
	{
		$this->form->setSession(APP_CONST_ADMIN_AUTH_NAME, null);
		//session_unset();
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

	protected function jsonPage(&$json_data, $security_token=true)
	{
		if ($security_token) $json_data['security_token'] = $this->createSecurityCode();

		if (preg_match("/^5\.3/", PHP_VERSION)) {
			$this->form->set('data', json_encode($json_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
		} else {
			$this->form->set('data', json_encode($json_data));
		}

		$this->resp->setContentType(SpResponse::CTYPE_JSON);
		$this->resp->setHeader('X-Content-Type-Options', 'nosniff');

		return $this->forward('json', APP_CONST_EMPTY_FRAME);
	}

	protected function checkXHR()
	{
		return ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
	}

	protected function setPageData($cate, $name)
	{
		$this->form->setSp('pageData', array('c'=>$cate, 'n'=>$name));
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
}
?>