<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('facebook.php', 'libs/facebook-php-sdk/src');
/**
 * ユーザー新規登録(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserSignupController extends BaseController
{
	/**
	 * そのままログイン
	 */
//	public function index()
//	{
//		$key = $this->form->get('key');
//		if (strlen($key) != 40) return $this->notfound();
//
//		$usersDao = new UsersDao($this->db);
//		$usersDao->addWhereStr(UsersDao::COL_USER_KEY, $key);
//		$usersDao->addWhere(UsersDao::COL_OPEN_LOGIN, 0, '>');
//		$usersDao->addWhere(UsersDao::COL_STATUS, UsersDao::STATUS_TEMP);
//		$usersDao->addWhere(UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);
//		$user = $usersDao->selectRow();
//		if (empty($user)) return $this->notfound();
//
//		try
//		{
//			$this->db->beginTransaction();
//
//			$usersDao->reset();
//			$usersDao->addValue(UsersDao::COL_STATUS, UsersDao::STATUS_REGULAR);
//			$usersDao->addValue(UsersDao::COL_LASTUPDATE, Dao::DATE_NOW);
//			$usersDao->addWhere(UsersDao::COL_USER_ID, $user['user_id']);
//			$usersDao->addWhereStr(UsersDao::COL_USER_KEY, $key);
//			$usersDao->addWhere(UsersDao::COL_STATUS, UsersDao::STATUS_TEMP);
//			if ($usersDao->doUpdate())
//			{
//				$userRanksDao = new UserRanksDao($this->db);
//				$userRanksDao->delete($user['user_id']);
//				$userRanksDao->reset();
//				$userRanksDao->addValue(UserRanksDao::COL_USER_ID, $user['user_id']);
//				$userRanksDao->doInsert();
//
//				$this->db->commit();
//				$this->db->closeConnection();
//			}
//			else
//			{
//				$this->db->rollback();
//			}
//		}
//		catch (SpException $e)
//		{
//			$this->logger->exception($e);
//			$this->db->rollback();
//			return $this->errorPage('システムエラーが発生しました。');
//		}
//
//		// 認証時のSESSIONから
//		$user[APP_CONST_FACEBOOK_OAUTH_CODE_NAME] = $this->form->get(APP_CONST_FACEBOOK_OAUTH_CODE_NAME);
//		$user[APP_CONST_FACEBOOK_OAUTH_TOKEN_NAME] = $this->form->get(APP_CONST_FACEBOOK_OAUTH_TOKEN_NAME);
//		$this->resp->sessionChangeId();
//		$this->setUserInfo($user);
//		if ($this->form->get('rd_url') == '') {
//			$redirect = APP_CONST_USER_LOGIN_FIRST_PAGE;
//		} else {
//			$redirect = $this->form->get('rd_url');
//			$this->form->clearSession('rd_url');
//		}
//		return $this->resp->sendRedirect($redirect);
//	}

	/**
	 * 初回登録画面
	 */
	public function index()
	{
		// facebook callback
		if ($this->form->get('state') != '' && $this->form->get('code') != '')
		{
			return $this->_facebook_share();
		}

		$key = $this->form->get('key');
		if (strlen($key) != 40) return $this->notfoundPage();

		$usersDao = new UsersDao($this->db);
		$usersDao->addWhereStr(UsersDao::COL_USER_KEY, $key);
		$usersDao->addWhere(UsersDao::COL_OPEN_LOGIN, 0, '>');
		$usersDao->addWhere(UsersDao::COL_STATUS, UsersDao::STATUS_TEMP);
		$usersDao->addWhere(UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);
		$user = $usersDao->selectRow();
		if (empty($user)) return $this->notfound();

		if ($this->form->isPostMethod())
		{
			if ($this->_validate() === false)
			{
				$this->form->set('errors', $this->form->getValidateErrors());
			}
			else
			{
				try
				{
					$this->db->beginTransaction();

					$usersDao->reset();
					$usersDao->addValue(UsersDao::COL_STATUS, UsersDao::STATUS_REGULAR);
					if ($this->form->get('is_edit_profile_msg') == 1)
					{
						$usersDao->addValueStr(UsersDao::COL_PROFILE_MSG, $this->form->get('profile_msg'));
						$user['profile_msg'] = $this->form->get('profile_msg');
					}
					if ($this->form->get('is_edit_mail_to') == 1)
					{
						$usersDao->addValue(UsersDao::COL_CONSULT_MAIL_TO, $this->form->get('consult_mail_to'));
						$usersDao->addValue(UsersDao::COL_CONSULT_REPLY_TO, $this->form->get('consult_reply_to'));
						$usersDao->addValue(UsersDao::COL_ADVICE_REPLY_TO, $this->form->get('advice_reply_to'));
						$usersDao->addValue(UsersDao::COL_CONSULT_REVIEW_TO, $this->form->get('consult_review_to'));
						$user['consult_mail_to'] = $this->form->get('consult_mail_to');
						$user['consult_reply_to'] = $this->form->get('consult_reply_to');
						$user['advice_reply_to'] = $this->form->get('advice_reply_to');
						$user['consult_review_to'] = $this->form->get('consult_review_to');
					}
					$usersDao->addValue(UsersDao::COL_SIGNUP_FB_SHARE, $this->form->get('signup_fb_share'));
					$usersDao->addValue(UsersDao::COL_LASTUPDATE, Dao::DATE_NOW);
					$usersDao->addWhere(UsersDao::COL_USER_ID, $user['user_id']);
					$usersDao->addWhereStr(UsersDao::COL_USER_KEY, $key);
					$usersDao->addWhere(UsersDao::COL_STATUS, UsersDao::STATUS_TEMP);
					if ($usersDao->doUpdate())
					{
						$userRanksDao = new UserRanksDao($this->db);
						$userRanksDao->addValue(UserRanksDao::COL_USER_ID, $user['user_id']);
						$userRanksDao->doInsert();

						$this->db->commit();
						$this->db->closeConnection();

						// 認証保存オプション
						if ($this->form->getCookieInt('rememberme') == 1) {
							$this->loginSession(APP_CONST_USER_REMEMBER_DAY * 86400);
						} else {
							$this->loginSession(0);
						}

						$this->setUserInfo($user);

						// Facebookへシェア
						if ($this->form->get('signup_fb_share') == 1)
						{
							$facebook = new Facebook(array(
								'appId'  => APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY,
								'secret' => APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET
							));
							$oauth_url = $this->getFbOAuth($facebook);
							return $this->resp->sendRedirect($oauth_url);
						}

						if ($this->form->get('rd_url') == '') {
							$redirect = APP_CONST_USER_LOGIN_FIRST_PAGE;
						} else {
							$redirect = $this->form->get('rd_url');
							$this->form->clearSession('rd_url');
						}
						return $this->resp->sendRedirect($redirect);
					}
				}
				catch (SpException $e)
				{
					$this->logger->exception($e);
				}
			}
		}

		$this->form->set('user', $user);
		if ($this->form->isGetMethod())
		{
			$this->form->setDefault('profile_msg', $user['profile_msg']);
			$this->form->setDefault('consult_mail_to', $user['consult_mail_to']);
			$this->form->setDefault('consult_reply_to', $user['consult_reply_to']);
			$this->form->setDefault('advice_reply_to', $user['advice_reply_to']);
			$this->form->setDefault('consult_review_to', $user['consult_review_to']);
			$this->form->setDefault('signup_fb_share', '1');
		}

		$this->form->setParameterForm('key');
		$this->form->setParameterForm('is_edit_profile_msg');
		$this->form->setParameterForm('is_edit_mail_to');

		$this->form->set('htitle', 'サインアップ');
		$this->setTitle($this->form->get('htitle'));

		$this->form->setScript($this->form->get('JS_URL').'/js/adviner.onload.js');

		$this->resp->noCache();

		return $this->forward('user/signup/user_signup_index', APP_CONST_MAIN_FRAME);
	}

	private function _facebook_share()
	{
		if ($this->checkUserAuth() === false) return $this->notfound();

		$facebook = new Facebook(array(
			'appId'  => APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY,
			'secret' => APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET
		));

		try
		{
			if ($facebook->getUser())
			{
				/**
				 * https://developers.facebook.com/docs/reference/api/post/
				 */
				$response = $facebook->api('/me/feed', 'POST', array(
						'link' => constant('app_site_url'),
						'name' => APP_CONST_SITE_SHARE_MSG,
						'description' => APP_CONST_META_DESCRIPTION,
						'picture' => constant('app_site_real_url').'img/fb_page.png'
					)
				);
				//$this->logger->debug($response);
			}
		}
		catch (FacebookApiException $e)
		{
			$this->logger->exception($e);
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
		}

		if ($this->form->get('rd_url') == '') {
			$redirect = APP_CONST_USER_LOGIN_FIRST_PAGE;
		} else {
			$redirect = $this->form->get('rd_url');
		}
		$this->form->clearSession('rd_url');

		return $this->resp->sendRedirect($redirect);
	}

//	public function fb_share()
//	{
//		if ($this->checkUserAuth() === false) return $this->notfound();
//
//		if ($this->form->get('error_reason') == 'user_denied')
//		{
//			$this->logger->error($this->form->get('error').': '.$this->form->get('error_description'));
//			return $this->resp->sendRedirect('/');
//		}
//
//		// from facebook
//		$code = $this->form->get('code');
//		if (empty($code)) return $this->notfound();
//
//		try
//		{
//			$token_url = 'https://graph.facebook.com/oauth/access_token'
//					. '?client_id=' . APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY
//					. '&client_secret=' . APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET
//					. '&redirect_uri=' . urlencode(constant('app_site_url').'user/signup/fb_share')
//					. '&code=' . $code;
//
//			$token_err = '';
////			$response = @file_get_contents($token_url);
//			$response = Util::getUrlContents($token_url, $token_err);
//			if ($response === false) throw new SpException('Facebook API(access_token) に接続できません: '.$token_err);
//			$params = array();
//			parse_str($response, $params);
//
//			$facebook = new Facebook(array(
//				'appId'  => APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY,
//				'secret' => APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET
//			));
//
//			$access_token = $params['access_token'];
//
//			if ($access_token)
//			{
//				$facebook->setAccessToken($access_token);
//				/**
//				 * https://developers.facebook.com/docs/reference/api/post/
//				 */
//				$response = $facebook->api('/me/feed', 'POST', array(
//						'message' => APP_CONST_SITE_SHARE_MSG,
//						'link' => constant('app_site_url'),
//						'name' => APP_CONST_SITE_TITLE_TOP,
//						'description' => APP_CONST_META_DESCRIPTION,
//						'picture' => constant('app_site_url').'img/fb_page.png'
//					)
//				);
//				//$this->logger->debug($response);
//			}
//		}
//		catch (FacebookApiException $e)
//		{
//			$this->logger->error($e->getMessage());
//		}
//		catch (SpException $e)
//		{
//			$this->logger->exception($e);
//		}
//
//		if ($this->form->get('rd_url') == '') {
//			$redirect = APP_CONST_USER_LOGIN_FIRST_PAGE;
//		} else {
//			$redirect = $this->form->get('rd_url');
//			$this->form->clearSession('rd_url');
//		}
//
//		return $this->resp->sendRedirect($redirect);
//	}

	/**
	 * 入力チェック
	 */
	private function _validate()
	{
		$ret = $this->form->validate($this->form->getValidates(0));

//		if ($this->form->get('is_edit_profile_msg') == 1)
//		{
//			$v= array(
//				array('profile_msg', '自己紹介を入力してください。', 'required')
//			);
//			if ($this->form->validate($v) === false) $ret = false;
//		}

		return $ret;
	}
}
?>
