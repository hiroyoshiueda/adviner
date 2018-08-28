<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('NoticesDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
/**
 * アドバイスくださいAPI(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiPleaseController extends BaseController
{
	const PAGE_LIMIT = 10;

	/**
	 * アドバイスフォーム
	 */
	public function get_advice_form()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$consult_id = $this->form->getInt('consult_id');
		if (empty($consult_id)) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
		);

		$is_security_token = false;

		$userInfo = $this->getUserInfo();

		try
		{
			// アドバイスくださいの確認
			$ConsultsDao = new ConsultsDao($this->db);
			$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_ID, $consult_id);
			$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_WAIT);
			$ConsultsDao->addWhere(ConsultsDao::COL_PLEASE_FLAG, ConsultsDao::PLEASE_FLAG_ON);
			$consult = $ConsultsDao->selectRow();
			if (empty($consult)) {
				$json_data['errmsg'] = '不正な呼出しです。';
				return $this->jsonPage($json_data, $is_security_token);
			} else if ($consult['consult_user_id'] == $userInfo['id']) {
				$json_data['errmsg'] = '本人にアドバイスはできません。';
				return $this->jsonPage($json_data, $is_security_token);
			}

			// アドバイザー（相談窓口登録済）の確認
			$AdvicesDao = new AdvicesDao($this->db);
			$AdvicesDao->addSelect(AdvicesDao::COL_ADVICE_ID);
			$AdvicesDao->addSelect(AdvicesDao::COL_ADVICE_TITLE);
			$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_USER_ID, $userInfo['id']);
			$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_OK);
			$advice_list = $AdvicesDao->select();

			$is_ok = false;
			$options = array();
			if (count($advice_list) > 0)
			{
				foreach ($advice_list as $d) {
					$options[$d['advice_id']] = $d['advice_title'];
				}
				$is_ok = true;
			}

			$tpl_vars = array(
				'consult' => $consult,
				'userInfo' => $userInfo,
				'advice_options' => $options,
				'is_ok' => $is_ok
			);

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/please/api_please_get_advice_form');
			$json_data['result'] = 1;
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
		}

		return $this->jsonPage($json_data, $is_security_token);
	}

	/**
	 * アドバイスください投稿
	 */
	public function post_advice()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$advice_id = $this->form->getInt('advice_id');
		$consult_id = $this->form->getInt('consult_id');
		if (empty($advice_id) || empty($consult_id)) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
//			'security_token' => '',
			'redirect' =>''
		);

		$userInfo = $this->getUserInfo();

		if ($this->_validateAdvice() === false)
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
				// アドバイスくださいの確認
				$ConsultsDao = new ConsultsDao($this->db);
				$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_ID, $consult_id);
				$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_WAIT);
				$ConsultsDao->addWhere(ConsultsDao::COL_PLEASE_FLAG, ConsultsDao::PLEASE_FLAG_ON);
				$consult = $ConsultsDao->selectRow();
				if (empty($consult)) return $this->notfound();

				// アドバイザー（相談窓口登録済）の確認
				$AdvicesDao = new AdvicesDao($this->db);
				$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $advice_id);
				$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_USER_ID, $userInfo['id']);
				$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_OK);
				$advice = $AdvicesDao->selectRow();
				if (empty($advice)) return $this->notfound();

				$nowdate = date("Y-m-d H:i:s");
				// 7日後に自動終了
				$finishdate = date("Y-m-d", time() + 604800) . ' 23:59:59';

				$this->db->beginTransaction();

				$ConsultReplysDao = new ConsultReplysDao($this->db);
				$ConsultReplysDao->addValue(ConsultReplysDao::COL_REPLY_STATUS, ConsultReplysDao::REPLY_STATUS_ADVISOR);
				$ConsultReplysDao->addValue(ConsultReplysDao::COL_ADVICE_ID, $advice['advice_id']);
				$ConsultReplysDao->addValue(ConsultReplysDao::COL_ADVICE_USER_ID, $advice['advice_user_id']);
				$ConsultReplysDao->addValue(ConsultReplysDao::COL_CONSULT_ID, $consult['consult_id']);
				$ConsultReplysDao->addValue(ConsultReplysDao::COL_CONSULT_USER_ID, $consult['consult_user_id']);
				$ConsultReplysDao->addValue(ConsultReplysDao::COL_CONSULT_PUBLIC_FLAG, $consult['public_flag']);
				$ConsultReplysDao->addValue(ConsultReplysDao::COL_FROM_USER_ID, $userInfo['id']);
				$ConsultReplysDao->addValue(ConsultReplysDao::COL_TO_USER_ID, $consult['consult_user_id']);
				$ConsultReplysDao->addValue(ConsultReplysDao::COL_REPLY_OPT, ConsultReplysDao::REPLY_OPT_ADVICE);
				$ConsultReplysDao->addValueStr(ConsultReplysDao::COL_REPLY_BODY, $this->form->get('reply_body'));
				$ConsultReplysDao->addValueStr(ConsultReplysDao::COL_CREATEDATE, $nowdate);
				$ConsultReplysDao->addValueStr(ConsultReplysDao::COL_LASTUPDATE, $nowdate);
				$ConsultReplysDao->doInsert();

				$consult_reply_id = $ConsultReplysDao->getLastInsertId();

				$ConsultsDao->reset();
				$ConsultsDao->addValue(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_DURING);
				$ConsultsDao->addValue(ConsultsDao::COL_ADVICE_ID, $advice['advice_id']);
				$ConsultsDao->addValue(ConsultsDao::COL_ADVICE_USER_ID, $advice['advice_user_id']);
				$ConsultsDao->addValue(ConsultsDao::COL_LATEST_REPLY_ID, $consult_reply_id);
				$ConsultsDao->addValue(ConsultsDao::COL_REVIEW_STATE, ConsultsDao::REVIEW_STATE_WAIT);
				$ConsultsDao->addValueStr(ConsultsDao::COL_LATEST_REPLY_BODY, $this->form->get('reply_body'));
				$ConsultsDao->addValueStr(ConsultsDao::COL_LATEST_REPLY_DATE, $nowdate);
				$ConsultsDao->addValueStr(ConsultsDao::COL_FINISHDATE, $finishdate);
				$ConsultsDao->addValueStr(ConsultsDao::COL_REPLYDATE, $nowdate);
				$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_ID, $consult_id);
				$ConsultsDao->doUpdate();

				$AdvicesDao->reset();
				$AdvicesDao->addValue(AdvicesDao::COL_CONSULT_TOTAL, AdvicesDao::COL_CONSULT_TOTAL.'+1');
				$AdvicesDao->addValue(AdvicesDao::COL_CONSULT_TODAY, AdvicesDao::COL_CONSULT_TODAY.'+1');
				$AdvicesDao->addValueStr(AdvicesDao::COL_REPLYDATE, $nowdate);
				$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $advice_id);
				$AdvicesDao->doUpdate();

				NoticesDao::postAdvice($this->db, $consult['consult_user_id'], $userInfo['id'], $userInfo['nickname'], $advice['advice_id'], $advice['advice_title'], $consult_id, $this->form->get('reply_body'));

				$this->db->commit();

				$json_data['result'] = 1;
				$json_data['redirect'] = '/advice/'.$advice_id.'/'.$consult_id.'/';
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$json_data['errmsg'] = 'システムエラーが発生しました。画面を更新してから再度実行してください。';
			}

			// メール通知
			if ($json_data['result'] == 1 && $consult['consult_user_id'] > 0)
			{
				$UsersDao = new UsersDao($this->db);
				$user = $UsersDao->getItem($consult['consult_user_id']);

				$is_reply_to = $user['advice_reply_to'];

				// 返信通知
				if ($is_reply_to == 1 && $user['email'] != '')
				{
					try
					{
						$mail_to = $user['email'];
						//$click_url = constant('app_site_url').'user/mypage/consult/'.$consult_id.'/';
						$click_url = constant('app_site_url').'advice/'.$advice_id.'/'.$consult_id.'/';
						$mail_arr = array(
							'user' => $user,
							'userInfo' => $userInfo,
							'reply_body' => $this->form->get('reply_body'),
							'click_url' => $click_url
						);
						$mail_title = '【'.APP_CONST_SITE_TITLE_S.'】'.$userInfo['nickname'].'さんからアドバイスが届いています';
						$mail_body = $this->form->getTemplateContents($mail_arr, '_mail/advice_reply_to');
						$mail_from = APP_CONST_SERVICE_EMAIL;
						$mail_from_name = APP_CONST_SITE_TITLE_S;
						$send_errmsg = '';
						if (Util::sendSmtpMail($mail_to, $mail_title, $mail_body, $mail_from, $mail_from_name, 'UTF-8', $send_errmsg) === false) {
							$this->logger->error("アドバイス通知：メール送信に失敗しました。[To:${mail_to}, From:${mail_from}]\n".$send_errmsg);
						}
					}
					catch (SpException $e)
					{
						$this->logger->exception($e);
					}
				}
			}

		}

		return $this->jsonPage($json_data, false);
	}

	/**
	 * 入力チェック
	 */
	private function _validateAdvice()
	{
		$ret = $this->form->validate($this->form->getValidates(0));
		return $ret;
	}
}
?>
