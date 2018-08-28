<?php
Sp::import('UsersDao', 'dao');
Sp::import('facebook.php', 'libs/facebook-php-sdk/src');
/**
 * Facebookサインイン(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class SessionsFacebookController extends BaseController
{
	/**
	 * 認証開始
	 */
	public function index()
	{
		$at = $this->form->get('at');
		if (empty($at)) return $this->notfound();

		$facebook = new Facebook(array(
			'appId'  => APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY,
			'secret' => APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET
		));

		$facebook->setAccessToken($at);

		$redirect = '/';
		$fb = array();

		try
		{
			if ($facebook->getUser())
			{
				$fb = $facebook->api('/me');

				if ($fb['id'] != '')
				{
					$usersDao = new UsersDao($this->db);
					$usersDao->addWhereStr(UsersDao::COL_OPEN_ID, $fb['id']);
					$usersDao->addWhere(UsersDao::COL_OPEN_LOGIN, UsersDao::OPEN_LOGIN_FACEBOOK);
					$usersDao->addWhere(UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);
					$user = $usersDao->selectRow();

					// 使用禁止アカウント
					if ($user[UsersDao::COL_DISPLAY_FLAG] == UsersDao::DISPLAY_FLAG_OFF)
					{
						return $this->errorPage('このアカウントは使用できません。');
					}
					else
					{
						$is_newuser = (empty($user) || $user[UsersDao::COL_STATUS] == UsersDao::STATUS_TEMP);
						// 画像URL
						$img_url = 'https://graph.facebook.com/'.$fb['id'].'/picture';
						// 性別
						$gender = $fb['gender'] == 'male' ? 1 : 2;
						// 生年月日(MM/DD/YYYY)
						if ($fb['birthday'] != '') {
							$dt_arr = explode('/', $fb['birthday']);
							$birthday = count($dt_arr)==3 ? sprintf("%04d-%02d-%02d", $dt_arr[2], $dt_arr[0], $dt_arr[1]) : '';
						} else {
							$birthday = '';
						}
						// URL
						$url = isset($fb['website']) ? $fb['website'] : '';
						// facebook URL
						$fb_url = isset($fb['link']) ? $fb['link'] : '';
						// usernameはデフォルトでは無い
						if (isset($fb['username']) && $fb['username'] != '') {
							$username = $fb['username'];
						} else {
							$username = $fb['first_name'] . '-' . $fb['last_name'];
							if ($username == '-') $username = '';
						}
						// 氏名
						$nickname = $fb['name'];

						// 日本語氏名を取得する
						try
						{
							//https://api.facebook.com/method/fql.query?access_token=アクセストークン&query=FQLの文
							$fql_url = 'https://api.facebook.com/method/fql.query?access_token=' . $facebook->getAccessToken()
									. '&query=' . urlencode('SELECT name FROM profile WHERE id = me()');
							$fql_err = '';
							$fql_res = Util::getUrlContents($fql_url, $fql_err);
							if ($fql_res === false) throw new SpException('Facebook API(fql) に接続できません: '.$fql_err);
							$fql_fb = @simplexml_load_string($fql_res);
							if ($fql_fb !== false && isset($fql_fb->profile->name) && $fql_fb->profile->name != '')
							{
								$nickname = $fql_fb->profile->name;
							}
						}
						catch (SpException $e)
						{
							$this->logger->exception($e);
						}

						// 検索用
						$searchname = str_replace(' ', '', $nickname);

						$this->db->beginTransaction();

						$usersDao->reset();
						$usersDao->addValueStr(UsersDao::COL_LOGIN, $username);
						$usersDao->addValueStr(UsersDao::COL_EMAIL, $fb['email']);
						$usersDao->addValueStr(UsersDao::COL_NICKNAME, $nickname);
						$usersDao->addValueStr(UsersDao::COL_SEARCHNAME, $searchname);
						$usersDao->addValue(UsersDao::COL_OPEN_LOGIN, UsersDao::OPEN_LOGIN_FACEBOOK);
						$usersDao->addValueStr(UsersDao::COL_OPEN_ID, $fb['id']);
						$usersDao->addValueStr(UsersDao::COL_OPEN_URL, $fb_url);
						$usersDao->addValueStr(UsersDao::COL_OPEN_IMAGE_URL, $img_url);
						$usersDao->addValueStr(UsersDao::COL_BIRTHDAY, $birthday);
						$usersDao->addValue(UsersDao::COL_GENDER, $gender);
						$usersDao->addValueStr(UsersDao::COL_URL, $url);
						$usersDao->addValueStr(UsersDao::COL_PROFILE_PATH, $img_url.'?type=normal');
						$usersDao->addValueStr(UsersDao::COL_PROFILE_S_PATH, $img_url.'?type=square');
						$usersDao->addValueStr(UsersDao::COL_PROFILE_B_PATH, $img_url.'?type=large');
						$usersDao->addValue(UsersDao::COL_LOGINDATE, Dao::DATE_NOW);
						$usersDao->addValue(UsersDao::COL_LASTUPDATE, Dao::DATE_NOW);

						// 新規登録
						if ($is_newuser)
						{
							$usersDao->addValue(UsersDao::COL_STATUS, UsersDao::STATUS_TEMP);
							$usersDao->addValueStr(UsersDao::COL_PROFILE_MSG, $fb['bio']);
							// 40桁
							$user_key = md5(Util::uniqId()) . substr(time(), -8);
							$usersDao->addValueStr(UsersDao::COL_USER_KEY, $user_key);
							if (isset($user['user_id']) && $user['user_id']>0) {
								$usersDao->addWhere(UsersDao::COL_USER_ID, $user['user_id']);
								$usersDao->doUpdate();
							} else {
								$usersDao->addValue(UsersDao::COL_CONSULT_MAIL_TO, 1);
								$usersDao->addValue(UsersDao::COL_CONSULT_REPLY_TO, 1);
								$usersDao->addValue(UsersDao::COL_ADVICE_REPLY_TO, 1);
								$usersDao->addValue(UsersDao::COL_CONSULT_REVIEW_TO, 1);
								$usersDao->addValue(UsersDao::COL_CHARGE_RATE, APP_CONST_USER_DEFAULT_CHARGE_RATE);
								$usersDao->addValue(UsersDao::COL_CREATEDATE, Dao::DATE_NOW);
								$usersDao->doInsert();
							}
							$this->db->commit();
							$redirect = '/user/signup/?key='.$user_key;
						}
						// 登録済み・現在のFBデータでアップデート
						else
						{
							if (isset($user['user_id']) && $user['user_id']>0) {
								$usersDao->addWhere(UsersDao::COL_USER_ID, $user['user_id']);
								$usersDao->doUpdate();
							}
							$this->db->commit();
							$usersDao->reset();
							$user = $usersDao->getItem($user['user_id']);
							// 認証保存オプション
							if ($this->form->getCookieInt('rememberme') == 1) {
								$this->loginSession(APP_CONST_USER_REMEMBER_DAY * 86400);
							} else {
								$this->loginSession(0);
							}
							$this->setUserInfo($user);
							if ($this->form->get('rd_url') == '') {
								$redirect = APP_CONST_USER_LOGIN_FIRST_PAGE;
							} else {
								$redirect = $this->form->get('rd_url');
								if (Util::startsWith($redirect, '/user/mypage/setting/')) {
									$redirect = 'https://'.$_SERVER['HTTP_HOST'].$redirect;
								}
							}
						}
					}
				}
			}
		}
		catch (FacebookApiException $e)
		{
			$this->logger->exception($e);
			return $this->errorPage('システムエラーが発生しました。['.$e->getMessage().']');
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			return $this->errorPage('システムエラーが発生しました。['.$e->getMessage().']');
		}

		$this->db->closeConnection();
		return $this->resp->sendRedirect($redirect);
	}
}
?>
