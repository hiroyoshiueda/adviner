<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
/**
 * 設定
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserSettingController extends BaseController
{
	/**
	 * 設定変更
	 */
	public function index()
	{
		if ($this->isForceHTTPS()) return $this->forceHTTPS();
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$userInfo = $this->getUserInfo();

		$this->form->set('user', $userInfo);
		$this->form->setDefault('consult_mail_to', $userInfo['consult_mail_to']);
		$this->form->setDefault('consult_reply_to', $userInfo['consult_reply_to']);
		$this->form->setDefault('advice_reply_to', $userInfo['advice_reply_to']);
		$this->form->setDefault('consult_review_to', $userInfo['consult_review_to']);
		$this->form->setDefault('profile_msg', $userInfo['profile_msg']);

		$this->setLoadUserRank(new UserRanksDao($this->db));

		$this->form->setParameterForm('is_edit_mailt_to');
		$this->form->setParameterForm('is_edit_profile_msg');

		$this->createSecurityCode();

		$this->form->set('htitle', 'プロフィール設定');
		$this->setTitle($this->form->get('htitle'), '設定');

		return $this->forward('user/setting/user_setting_index', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 設定保存(ajax)
	 */
	public function post_save_api()
	{
		$target_col = $this->form->get('target_col');

		if ($this->form->isGetMethod() || $this->checkUserAuth() === false || empty($target_col)) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'security_token' => ''
		);

		if ($this->_validate() === false)
		{
			$json_data['errmsg'] = '入力エラーがあります。';
			$json_data['errors'] = $this->form->getValidateErrors();
		}
		else if ($this->checkSecurityCode() === false)
		{
			$json_data['errmsg'] = self::ERROR_PAGE_MESSAGE5;
		}
		else
		{
			try
			{
				$usersDao = new UsersDao($this->db);
				if ($target_col == 'mail_to')
				{
					$usersDao->addValue(UsersDao::COL_CONSULT_MAIL_TO, ($this->form->get('consult_mail_to')==1 ? 1 : 0));
					$usersDao->addValue(UsersDao::COL_CONSULT_REPLY_TO, ($this->form->get('consult_reply_to')==1 ? 1 : 0));
					$usersDao->addValue(UsersDao::COL_ADVICE_REPLY_TO, ($this->form->get('advice_reply_to')==1 ? 1 : 0));
					$usersDao->addValue(UsersDao::COL_CONSULT_REVIEW_TO, ($this->form->get('consult_review_to')==1 ? 1 : 0));
				}
				else if ($target_col == 'profile_msg')
				{
					$usersDao->addValueStr(UsersDao::COL_PROFILE_MSG, $this->form->get('profile_msg'));
				}
				$usersDao->addValue(UsersDao::COL_LASTUPDATE, Dao::DATE_NOW);
				$usersDao->addWhere(UsersDao::COL_USER_ID, $userInfo['id']);
				$usersDao->doUpdate();

				$new_info = array();

				if ($target_col == 'mail_to')
				{
					$new_info = array(
						'consult_mail_to' => ($this->form->get('consult_mail_to')==1 ? 1 : 0),
						'consult_reply_to' => ($this->form->get('consult_reply_to')==1 ? 1 : 0),
						'advice_reply_to' => ($this->form->get('advice_reply_to')==1 ? 1 : 0),
						'consult_review_to' => ($this->form->get('consult_review_to')==1 ? 1 : 0)
					);
					$vars = array(
						'user' => $new_info
					);
					$json_data['html'] = $this->form->getTemplateContents($vars, 'user/setting/_mail_to_view');
				}
				else if ($target_col == 'profile_msg')
				{
					$new_info = array(
						'profile_msg' => $this->form->get('profile_msg')
					);
					$vars = array(
						'user' => $new_info
					);
					$json_data['html'] = $this->form->getTemplateContents($vars, 'user/setting/_profile_msg_view');
				}
				$json_data['result'] = 1;

				$this->updateUserInfo($new_info);
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$json_data['errmsg'] = 'システムエラーが発生しました。画面を更新してから再度実行してください。';
			}
		}

		return $this->jsonPage($json_data, true);
	}

	/**
	 * 入力チェック
	 */
	private function _validate()
	{
		$target_col = $this->form->get('target_col');

		if ($target_col == 'mail_to')
		{
			$ret = true;
		}
		else if ($target_col == 'profile_msg')
		{
			$ret = $this->form->validate($this->form->getValidates(0));
		}

		return $ret;
	}
}
?>
