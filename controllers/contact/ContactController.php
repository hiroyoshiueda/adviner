<?php
Sp::import('UsersDao', 'dao');
Sp::import('ContactsDao', 'dao');
/**
 * 問い合わせ(Controller)
 * @author Hiroyoshi
 */
class ContactController extends BaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$this->form->set('htitle', 'お問い合わせ');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('contact/contact_index', APP_CONST_MAIN_FRAME);
	}

	public function form()
	{
		$this->createSecurityCode();

		$this->form->set('htitle', 'お問い合わせフォーム');
		$this->setTitle($this->form->get('htitle'));

		$this->form->setScript($this->form->get('JS_URL').'/js/adviner.onload.js');

		return $this->forward('contact/contact_form', APP_CONST_MAIN_FRAME);
	}

	public function complete()
	{
		if ($this->form->isGetMethod()) return $this->notfound();

		if ($this->checkSecurityCode() === false)
		{
			return $this->errorPage(self::ERROR_PAGE_MESSAGE5);
		}
		else if ($this->_validateSend() === false)
		{
			$this->form->set('errors', $this->form->getValidateErrors());
			return $this->form();
		}

		$userInfo = $this->getUserInfo();
		$user_info_str = empty($userInfo) ? '' : serialize($userInfo);
		$ip = $_SERVER['REMOTE_ADDR'];
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$nowtime = date('Y-m-d H:i:s');

		try
		{
			$this->db->beginTransaction();

			$dao = new ContactsDao($this->db);
			$dao->addValue(ContactsDao::COL_STATUS, ContactsDao::STATUS_BASIC);
			$dao->addValueStr(ContactsDao::COL_SUBJECT, $this->form->get('subject'));
			$dao->addValueStr(ContactsDao::COL_BODY, $this->form->get('body'));
			$dao->addValueStr(ContactsDao::COL_USEREMAIL, $this->form->get('useremail'));
			$dao->addValueStr(ContactsDao::COL_USERNAME, $this->form->get('username'));
			$dao->addValueStr(ContactsDao::COL_USERINFO, $user_info_str);
			$dao->addValueStr(ContactsDao::COL_USERAGENT, $ip."\n".$agent);
			$dao->addValueStr(ContactsDao::COL_CREATEDATE, $nowtime);
			$dao->doInsert();

			$this->db->commit();

		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$this->db->rollback();
			$this->form->setValidateErrors('msg', 'エラーが発生しました。しばらくしてからもう一度お試しください。');
			$this->form->set('sys_errors', $this->form->getValidateErrors());
			return $this->form();
		}

		// 確認メール送信
		try
		{
			$mail_arr = $this->form->getAll();
			$mail_arr['REMOTE_ADDR'] = $ip;
			$mail_arr['HTTP_USER_AGENT'] = $agent;
			$mail_arr['nowtime'] = $nowtime;

			$mail_to = APP_CONST_CONTACT_TO_EMAIL;
			$mail_title = '【お問い合わせ】';
			$mail_body = $this->form->getTemplateContents($mail_arr, '_mail/contact_send');
			$mail_from = APP_CONST_INFO_EMAIL;
			$mail_from_name = APP_CONST_SITE_TITLE_S;
			$send_errmsg = '';
			if (Util::sendSmtpMail($mail_to, $mail_title, $mail_body, $mail_from, $mail_from_name, 'UTF-8', $send_errmsg) === false) {
				$this->logger->error("メール送信に失敗 [To:${mail_to}, From:${mail_from}]\n".$send_errmsg);
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
		}

		$this->form->set('htitle', 'お問い合わせフォーム（送信完了）');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('contact/contact_complete', APP_CONST_MAIN_FRAME);
	}

	private function _validateSend()
	{
		$ret = $this->form->validate($this->form->getValidates(0));
		return $ret;
	}

	/**
	 * Feedback送信
	 */
	public function feedback_api()
	{
		if ($this->form->isGetMethod()) return $this->notfound();

		$userInfo = $this->getUserInfo();
		$user_info_str = empty($userInfo) ? '' : serialize($userInfo);
		$ip = $_SERVER['REMOTE_ADDR'];
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$nowtime = date('Y-m-d H:i:s');
		$body = $this->form->get('body');

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'security_token' => ''
		);

		if ($this->_valdateFeedbackApi() === false)
		{
			$json_data['errmsg'] = '入力エラーがあります。';
			$json_data['errors'] = $this->form->getValidateErrors();
		}
//		else if ($this->checkSecurityCode() === false)
//		{
//			$json_data['errmsg'] = self::ERROR_PAGE_MESSAGE5;
//		}
		else
		{
			try
			{
				$contactsDao = new ContactsDao($this->db);
				$contactsDao->addValue(ContactsDao::COL_STATUS, ContactsDao::STATUS_QUICK);
				$contactsDao->addValueStr(ContactsDao::COL_SUBJECT, '');
				$contactsDao->addValueStr(ContactsDao::COL_BODY, $body);
				$contactsDao->addValueStr(ContactsDao::COL_USEREMAIL, '');
				$contactsDao->addValueStr(ContactsDao::COL_USERNAME, '');
				$contactsDao->addValueStr(ContactsDao::COL_USERINFO, $user_info_str);
				$contactsDao->addValueStr(ContactsDao::COL_USERAGENT, $ip."\n".$agent);
				$contactsDao->addValueStr(ContactsDao::COL_CREATEDATE, $nowtime);
				$contactsDao->doInsert();

				$json_data['result'] = 1;
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$json_data['errmsg'] = 'システムエラーが発生しました。画面を更新してから再度実行してください。';
			}
		}

		return $this->jsonPage($json_data, false);
	}

	private function _valdateFeedbackApi()
	{
		$ret = $this->form->validate($this->form->getValidates(1));
		return $ret;
	}
}
?>
