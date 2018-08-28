<?php
Sp::import('UsersDao', 'dao');
Sp::import('PEAR/HTTP/OAuth/Consumer.php', 'libs');
Sp::import('PEAR/Services/Twitter.php', 'libs');
/**
 * Twitterサインイン(Controller)
 * @author Hiroyoshi
 */
class TwitterController extends BaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		if ($this->checkUserAuth()) return $this->resp->sendRedirect(APP_CONST_USER_LOGIN_FIRST_PAGE);

		$auth_url = '';
		try {
			$oauth = new HTTP_OAuth_Consumer(APP_CONST_TWITTER_OAUTH_CONSUMER_KEY, APP_CONST_TWITTER_OAUTH_CONSUMER_SECRET);
			$oauth->getRequestToken('http://twitter.com/oauth/request_token', constant('app_site_url') . APP_CONST_TWITTER_OAUTH_CALLBACK_PATH);
			$this->form->setSession('twitter_request_token', $oauth->getToken());
			$this->form->setSession('twitter_request_token_secret', $oauth->getTokenSecret());
			$auth_url = $oauth->getAuthorizeUrl('http://twitter.com/oauth/authenticate');
		} catch (SpException $e) {
			$this->logger->exception($e);
			return $this->errorPage('システムエラーが発生しました。['.$e->getMessage().']');
		}
		return $this->resp->sendRedirect($auth_url);
	}

	/**
	 * Twitterサイトからのコールバック先
	 */
	public function signin()
	{
		$verifier = $this->form->get('oauth_verifier');
		if (empty($verifier)) return $this->notfoundPage();

		try {
			$oauth = new HTTP_OAuth_Consumer(APP_CONST_TWITTER_OAUTH_CONSUMER_KEY, APP_CONST_TWITTER_OAUTH_CONSUMER_SECRET);
			$oauth->setToken($this->form->get('twitter_request_token'));
			$oauth->setTokenSecret($this->form->get('twitter_request_token_secret'));
			$oauth->getAccessToken('http://twitter.com/oauth/access_token', $verifier);
			$twitter_access_token = $oauth->getToken();
			$twitter_access_token_secret = $oauth->getTokenSecret();
			$this->form->setSession('twitter_access_token', $twitter_access_token);
			$this->form->setSession('twitter_access_token_secret', $twitter_access_token_secret);
			// 認証者情報
			$twitter = new Services_Twitter();
			$twitter->setOAuth($oauth);
			$twitterInfo = $twitter->account->verify_credentials();
			// twitterのUID
			if ($twitterInfo->id > 0) {
				$usersDao = new UsersDao($this->db);
				$usersDao->addWhere(UsersDao::COL_OPEN_LOGIN, UsersDao::OPEN_LOGIN_TWITTER);
				$usersDao->addWhereStr(UsersDao::COL_OPEN_ID, $twitterInfo->id);
				$usersDao->addWhere(UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);
				$user = $usersDao->selectRow();
				// 新規
				if (count($user)==0 || $user[UsersDao::COL_STATUS] == UsersDao::STATUS_TEMP) {

					$temp_key = md5(Util::uniqId());

					$usersDao->reset();
					$usersDao->addValue(UsersDao::COL_STATUS, UsersDao::STATUS_TEMP);
					$usersDao->addValueStr(UsersDao::COL_EMAIL, '');
					$usersDao->addValueStr(UsersDao::COL_LOGIN, $twitterInfo->screen_name);
					$usersDao->addValueStr(UsersDao::COL_PENNAME, $twitterInfo->name);
					$usersDao->addValueStr(UsersDao::COL_URL, $twitterInfo->url);
					$usersDao->addValueStr(UsersDao::COL_TWITTER_ID, $twitterInfo->screen_name);
					$usersDao->addValueStr(UsersDao::COL_PROFILE_MSG, $twitterInfo->description);
					$usersDao->addValueStr(UsersDao::COL_TEMP_KEY, $temp_key);
					$usersDao->addValue(UsersDao::COL_OPEN_LOGIN, UsersDao::OPEN_LOGIN_TWITTER);
					$usersDao->addValueStr(UsersDao::COL_OPEN_ID, $twitterInfo->id);
					$usersDao->addValueStr(UsersDao::COL_OPEN_IMAGE_URL, $twitterInfo->profile_image_url);
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
					$user['twitter_access_token'] = $twitter_access_token;
					$user['twitter_access_token_secret'] = $twitter_access_token_secret;
					$this->setUserInfo($user);
					return $this->resp->sendRedirect(APP_CONST_USER_LOGIN_FIRST_PAGE);
				}
			}
		} catch (HTTP_OAuth_Consumer_Exception_InvalidResponse $e) {
			$this->logger->error($e->getMessage());
			return $this->errorPage('システムエラーが発生しました。['.$e->getMessage().']');
		} catch (SpException $e) {
			$this->logger->exception($e);
			return $this->errorPage('システムエラーが発生しました。['.$e->getMessage().']');
		}
	}
}
?>
