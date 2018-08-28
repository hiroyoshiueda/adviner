<?php
// /libs/PEAR/Auth/OpenID/CryptUtil.php の対応
if (!@is_readable('/dev/urandom')) {
	define('Auth_OpenID_RAND_SOURCE', null);
}
Sp::import('UsersDao', 'dao');
Sp::import('PEAR/Auth/OpenID/Consumer.php', 'libs');
Sp::import('PEAR/Auth/OpenID/FileStore.php', 'libs');
Sp::import('PEAR/Auth/OpenID/SReg.php', 'libs');
Sp::import('PEAR/Auth/OpenID/PAPE.php', 'libs');
/**
 * OpenIDサインイン(Controller)
 * @author Hiroyoshi
 */
class OpenidController extends BaseController
{
	/**
	 * mixi
	 *
	 * ※修正
	 * /libs/PEAR/Auth/Yadis/ParanoidHTTPFetcher.php
	 */
	public function mixi()
	{
		$this->form->setSession('openid_login', UsersDao::OPEN_LOGIN_MIXI);
		return $this->_exec(APP_CONST_OPENID_MIXI);
	}

	private function _exec($openid)
	{
		if ($this->checkUserAuth()) return $this->resp->sendRedirect(APP_CONST_USER_LOGIN_FIRST_PAGE);

		$auth_url = '';

		try {

			$consumer = new Auth_OpenID_Consumer(new Auth_OpenID_FileStore(APP_CONST_OPENID_STORE_DIR));
			$auth_request = $consumer->begin($openid);
			if (!$auth_request) throw new SpException('OpenIDが正しくありません。');
			$sreg_request = Auth_OpenID_SRegRequest::build(array('nickname'), array('fullname', 'email'));
			if ($sreg_request) {
				$auth_request->addExtension($sreg_request);
			}

			$trust_root = constant('app_site_ssl_url') . APP_CONST_OPENID_TRUST_DIR;
			$return_to  = constant('app_site_ssl_url') . APP_CONST_OPENID_CALLBACK_PATH;
			// Simplityを使っている場合の対応
			$return_to .= '?page=openid/signin';

			if ($auth_request->shouldSendRedirect()) {
				$auth_url = $auth_request->redirectURL($trust_root, $return_to);
				if (Auth_OpenID::isFailure($auth_url)) {
					throw new SpException('サーバーにリダイレクトできません：'.$auth_url->message);
				}
			} else {
				$form_html = $auth_request->htmlMarkup($trust_root, $return_to, false, array('id' => 'openid_message'));
				if (Auth_OpenID::isFailure($form_html)) {
					throw new SpException('サーバーにリダイレクトできません(HTML)：'.$form_html->message);
				}
				$this->form->set('html', $form_html);
				return $this->forward('empty', APP_CONST_EMPTY_FRAME);
			}

		} catch (SpException $e) {
			$this->logger->exception($e);
			return $this->errorPage('システムエラーが発生しました。['.$e->getMessage().']');
		}

		return $this->resp->sendRedirect($auth_url);
	}

	/**
	 * コールバック先
	 */
	public function signin()
	{
		$return_to = constant('app_site_ssl_url') . APP_CONST_OPENID_CALLBACK_PATH;
		// Simplityを使っている場合の対応
		$return_to .= '?page='.$this->form->get('page');

		try {

			$consumer = new Auth_OpenID_Consumer(new Auth_OpenID_FileStore(APP_CONST_OPENID_STORE_DIR));
			$response = $consumer->complete($return_to);

			if ($response->status == Auth_OpenID_SUCCESS) {
				$open_id = $response->getDisplayIdentifier();
				$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
				$sreg = $sreg_resp->contents();
				if ($open_id!='') {
					$usersDao = new UsersDao($this->db);
					$usersDao->addWhere(UsersDao::COL_OPEN_LOGIN, $this->form->getInt('openid_login'));
					$usersDao->addWhereStr(UsersDao::COL_OPEN_ID, $open_id);
					$usersDao->addWhere(UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);
					$user = $usersDao->selectRow();
					// 新規
					if (count($user)==0 || $user[UsersDao::COL_STATUS] == UsersDao::STATUS_TEMP) {

						$temp_key = md5(Util::uniqId());

						$usersDao->reset();
						$usersDao->addValue(UsersDao::COL_STATUS, UsersDao::STATUS_TEMP);
						$usersDao->addValueStr(UsersDao::COL_EMAIL, '');
						//$usersDao->addValueStr(UsersDao::COL_LOGIN, $twitterInfo->screen_name);
						$usersDao->addValueStr(UsersDao::COL_PENNAME, $sreg['nickname']);
						//$usersDao->addValueStr(UsersDao::COL_URL, $twitterInfo->url);
						//$usersDao->addValueStr(UsersDao::COL_TWITTER_ID, $twitterInfo->screen_name);
						//$usersDao->addValueStr(UsersDao::COL_PROFILE_MSG, $twitterInfo->description);
						$usersDao->addValueStr(UsersDao::COL_TEMP_KEY, $temp_key);
						$usersDao->addValue(UsersDao::COL_OPEN_LOGIN, $this->form->getInt('openid_login'));
						$usersDao->addValueStr(UsersDao::COL_OPEN_ID, $open_id);
						$usersDao->addValueStr(UsersDao::COL_OPEN_IMAGE_URL, '');
						$usersDao->addValueStr(UsersDao::COL_OPEN_DATA, '');
						$usersDao->addValue(UsersDao::COL_LASTUPDATE, Dao::DATE_NOW);
						if ($user['user_id']>0) {
							$usersDao->addWhere(UsersDao::COL_USER_ID, $user['user_id']);
							$usersDao->doUpdate();
						} else {
							$usersDao->addValue(UsersDao::COL_CREATEDATE, Dao::DATE_NOW);
							$usersDao->doInsert();
						}
						return $this->resp->sendRedirect('/user/regist/newuser?key='.$temp_key);

					} else if ($user[UsersDao::COL_DISPLAY_FLAG] == UsersDao::DISPLAY_FLAG_OFF) {
						return $this->errorPage('このアカウントは使用できません。');

					// 既に登録済み
					} else {
						$this->resp->sessionChangeId();
						$this->setUserInfo($user);
						return $this->resp->sendRedirect(APP_CONST_USER_LOGIN_FIRST_PAGE);
					}
				}
			} else if ($response->status == Auth_OpenID_CANCEL) {
				$this->resp->sendRedirect('/login');
			} else if ($response->status == Auth_OpenID_FAILURE) {
				throw new SpException($response->message);
			}

		} catch (SpException $e) {
			$this->logger->exception($e);
			return $this->errorPage('システムエラーが発生しました。['.$e->getMessage().']');
		}
	}
}
?>
