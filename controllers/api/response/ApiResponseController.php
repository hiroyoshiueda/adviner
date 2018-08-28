<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('NoticesDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
/**
 * 返答API(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiResponseController extends BaseController
{
	/**
	 * 返信登録
	 */
	public function post_reply()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$consult_id = $this->form->getInt('consult_id');
		if (empty($consult_id)) return $this->notfound();

		$pagetype = $this->form->get('pagetype');
		$replynum = $this->form->getInt('replynum');

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
//			'security_token' => '',
			'is_finish' => 0,
			'is_public' => 0,
			'fb_share' => array()
		);

		$userInfo = $this->getUserInfo();

		if ($this->_validateReply() === false)
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
				$consultsDao = new ConsultsDao($this->db);
				$consult = $consultsDao->getItem($consult_id);
				// 相談者とアドバイザー以外には見せない
				if (empty($consult) || ($consult['advice_user_id'] != $userInfo['id'] && $consult['consult_user_id'] != $userInfo['id'])) {
					return $this->notfound();
				}

				// 相談スレッドが終了している場合
				if ($consult[ConsultsDao::COL_CONSULT_STATUS] != ConsultsDao::CONSULT_STATUS_DURING) {
					$json_data['errmsg'] = 'この相談スレッドは終了しました。これ以上の相談・アドバイスはできません。';
					return $this->jsonPage($json_data);
				}

				$advicesDao = new AdvicesDao($this->db);
				$advice = $advicesDao->getItemByUserId($consult['advice_id'], $consult['advice_user_id']);
				if (empty($advice)) return $this->notfound();

				if ($consult['advice_user_id'] == $userInfo['id'])
				{
					$status = ConsultReplysDao::REPLY_STATUS_ADVISOR;
					$to_user_id = $consult['consult_user_id'];
				}
				else
				{
					$status = ConsultReplysDao::REPLY_STATUS_CONSULT;
					$to_user_id = $consult['advice_user_id'];
				}

				$reply_opt = $this->form->get('reply_opt');
				$nowdate = date("Y-m-d H:i:s");

				$this->db->beginTransaction();

				$consultReplysDao = new ConsultReplysDao($this->db);
				$consultReplysDao->addValue(ConsultReplysDao::COL_REPLY_STATUS, $status);
				$consultReplysDao->addValue(ConsultReplysDao::COL_ADVICE_ID, $advice['advice_id']);
				$consultReplysDao->addValue(ConsultReplysDao::COL_ADVICE_USER_ID, $advice['advice_user_id']);
				$consultReplysDao->addValue(ConsultReplysDao::COL_CONSULT_ID, $consult['consult_id']);
				$consultReplysDao->addValue(ConsultReplysDao::COL_CONSULT_USER_ID, $consult['consult_user_id']);
				$consultReplysDao->addValue(ConsultReplysDao::COL_CONSULT_PUBLIC_FLAG, $consult[ConsultsDao::COL_PUBLIC_FLAG]);
				$consultReplysDao->addValue(ConsultReplysDao::COL_FROM_USER_ID, $userInfo['id']);
				$consultReplysDao->addValue(ConsultReplysDao::COL_TO_USER_ID, $to_user_id);
				$consultReplysDao->addValue(ConsultReplysDao::COL_REPLY_OPT, $reply_opt);
				$consultReplysDao->addValueStr(ConsultReplysDao::COL_REPLY_BODY, $this->form->get('reply_body'));
				$consultReplysDao->addValueStr(ConsultReplysDao::COL_CREATEDATE, $nowdate);
				$consultReplysDao->addValueStr(ConsultReplysDao::COL_LASTUPDATE, $nowdate);
				$consultReplysDao->doInsert();

				$consult_reply_id = $consultReplysDao->getLastInsertId();

				$consultsDao->reset();
				// アドバイザーからの返信の場合
				if ($status == ConsultReplysDao::REPLY_STATUS_ADVISOR)
				{
					// アドバイスできない場合は終了
					if ($reply_opt == ConsultReplysDao::REPLY_OPT_NOADVICE) {
						$consultsDao->addValue(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_FINISH);
						$consultsDao->addValue(ConsultsDao::COL_REVIEW_STATE, ConsultsDao::REVIEW_STATE_NOWRITE);
						$json_data['is_finish'] = 1;
					// アドバイスした場合
					} else if ($reply_opt == ConsultReplysDao::REPLY_OPT_ADVICE) {
						// 公開相談の場合
						if ($consult['public_flag'] == ConsultsDao::PUBLIC_FLAG_PUBLIC) {
							$consultsDao->addValue(ConsultsDao::COL_LATEST_REPLY_ID, $consult_reply_id);
							$json_data['is_public'] = 1;
						}
						$consultsDao->addValue(ConsultsDao::COL_REVIEW_STATE, ConsultsDao::REVIEW_STATE_WAIT);
					}
					$consultsDao->addValueStr(ConsultsDao::COL_LATEST_REPLY_BODY, $this->form->get('reply_body'));
					$consultsDao->addValueStr(ConsultsDao::COL_LATEST_REPLY_DATE, $nowdate);
				}
				$consultsDao->addValueStr(ConsultsDao::COL_REPLYDATE, $nowdate);
				$consultsDao->addWhere(ConsultsDao::COL_CONSULT_ID, $consult_id);
				$consultsDao->doUpdate();
// @todo この日付更新は将来いらないかも？
				$advicesDao->reset();
				$advicesDao->addValueStr(AdvicesDao::COL_REPLYDATE, $nowdate);
				$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $consult['advice_id']);
				$advicesDao->doUpdate();

				// 通知
				if ($status == ConsultReplysDao::REPLY_STATUS_ADVISOR)
				{
					if ($reply_opt == ConsultReplysDao::REPLY_OPT_ADVICE)
					{
						NoticesDao::postAdvice($this->db, $to_user_id, $userInfo['id'], $userInfo['nickname'], $advice['advice_id'], $advice['advice_title'], $consult_id, $this->form->get('reply_body'));
					}
					else if ($reply_opt == ConsultReplysDao::REPLY_OPT_NOADVICE)
					{
						NoticesDao::postNotAdvice($this->db, $to_user_id, $userInfo['id'], $userInfo['nickname'], $advice['advice_id'], $advice['advice_title'], $consult_id, $this->form->get('reply_body'));
					}
					else
					{
						NoticesDao::postReply($this->db, $to_user_id, $userInfo['id'], $userInfo['nickname'], $advice['advice_id'], $advice['advice_title'], $consult_id, $this->form->get('reply_body'));
					}
				}
				else
				{
					NoticesDao::postReply($this->db, $to_user_id, $userInfo['id'], $userInfo['nickname'], $advice['advice_id'], $advice['advice_title'], $consult_id, $this->form->get('reply_body'));
				}

				$this->db->commit();

				$consultReplysDao->reset();
				$item = $consultReplysDao->getItemOfMypageFromUser($consult_reply_id);

				$item['consult_id'] = $consult_id;

				$tpl_vars = array(
					'reply' => $item,
					'reply_user' => $userInfo,
					'userInfo' => $userInfo,
					'is_first' => false,
					'is_last' => true,
					'latest_reply_id' => 0,
					'REAL_URL' => $this->form->getSp('REAL_URL')
				);
				if ($consult['public_flag'] == ConsultsDao::PUBLIC_FLAG_PUBLIC)
				{
					if ($status == ConsultReplysDao::REPLY_STATUS_ADVISOR)
					{
						if ($consult['latest_reply_id'] > 0 || $reply_opt == ConsultReplysDao::REPLY_OPT_ADVICE) {
							$tpl_vars['latest_reply_id'] = $consult_reply_id;
						}
					}
					else
					{
						if ($consult['latest_reply_id'] > 0) {
							$tpl_vars['latest_reply_id'] = $consult_reply_id;
						}
					}
				}
				if ($replynum == 0) {
					$tpl_vars['is_first'] = true;
				}
				$json_data['html'] = $this->form->getTemplateContents($tpl_vars, '_parts/consult_thread/consult_thread_reply_item');
				$json_data['result'] = 1;
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$json_data['errmsg'] = parent::ERROR_AJAX_MESSAGE1;
			}

			// 通知
			if ($json_data['result'] == 1 && $to_user_id > 0)
			{
				$usersDao = new UsersDao($this->db);
				$user = $usersDao->getItem($to_user_id);

				$is_reply_to = ($status == ConsultReplysDao::REPLY_STATUS_ADVISOR) ? $user['advice_reply_to'] : $user['consult_reply_to'];

				// Facebookシェア
				if ($this->form->getInt('is_fb_share') == 1)
				{
					if ($consult['public_flag'] == ConsultsDao::PUBLIC_FLAG_PUBLIC)
					{
						if ($status == ConsultReplysDao::REPLY_STATUS_ADVISOR)
						{
							if ($consult['latest_reply_id'] > 0 || $reply_opt == ConsultReplysDao::REPLY_OPT_ADVICE) {
								$json_data['fb_share'] = array(
									'message' => '[アドバイス]'.$this->form->get('reply_body'),
									'link' => constant('app_site_real_url').'advice/'.$consult['advice_id'].'/'.$consult['consult_id'].'/',
									'name' => $user['nickname'].'さんにアドバイスしました。- '.APP_CONST_SITE_TITLE_F,
									'description' => $userInfo['nickname'].'さんが'.$user['nickname'].'さんにアドバイスしました。相談内容は「Adviner」上で閲覧することができます。',
									'picture' => constant('app_site_real_url').'img/fb_page.png'
								);
							}
						}
						else
						{
							if ($consult['latest_reply_id'] > 0) {
								$json_data['fb_share'] = array(
									'message' => '[相談]'.$this->form->get('reply_body'),
									'link' => constant('app_site_real_url').'advice/'.$consult['advice_id'].'/'.$consult['consult_id'].'/',
									'name' => $user['nickname'].'さんに相談しました。- '.APP_CONST_SITE_TITLE_F,
									'description' => $userInfo['nickname'].'さんが'.$user['nickname'].'さんに相談しました。相談内容は「Adviner」上で閲覧することができます。',
									'picture' => constant('app_site_real_url').'img/fb_page.png'
								);
							}
						}
					}

				}

				// 返信通知
				if ($is_reply_to == 1 && $user['email'] != '')
				{
					try
					{
						$mail_to = $user['email'];
						if ($advice['charge_flag'] == AdvicesDao::CHARGE_FLAG_CHARGE) {
							$click_url = constant('app_site_ssl_url').'advice/'.$consult['advice_id'].'/'.$consult['consult_id'].'/';
						} else {
							$click_url = constant('app_site_url').'advice/'.$consult['advice_id'].'/'.$consult['consult_id'].'/';
						}
						$mail_arr = array(
							'user' => $user,
							'userInfo' => $userInfo,
							'reply_body' => $this->form->get('reply_body'),
							'reply_opt' => $reply_opt,
							'consult_date' => $consult['createdate'],
							'charge_price' => $advice['charge_price'],
							'click_url' => $click_url
						);
						if ($status == ConsultReplysDao::REPLY_STATUS_ADVISOR) {
							if ($reply_opt == ConsultReplysDao::REPLY_OPT_NOADVICE) {
								$mail_title = '【'.APP_CONST_SITE_TITLE_S.'】'.$userInfo['nickname'].'さんから返信が届いています';
								$mail_body = $this->form->getTemplateContents($mail_arr, '_mail/advice_no_reply_to');
							} else if ($advice['charge_flag'] == AdvicesDao::CHARGE_FLAG_CHARGE) {
								$mail_title = '【'.APP_CONST_SITE_TITLE_S.'】'.$userInfo['nickname'].'さんから有料アドバイスが届いています';
								$mail_body = $this->form->getTemplateContents($mail_arr, '_mail/advice_charge_reply_to');
							} else {
								$mail_title = '【'.APP_CONST_SITE_TITLE_S.'】'.$userInfo['nickname'].'さんからアドバイスが届いています';
								$mail_body = $this->form->getTemplateContents($mail_arr, '_mail/advice_reply_to');
							}
						} else {
							$mail_title = '【'.APP_CONST_SITE_TITLE_S.'】'.$userInfo['nickname'].'さんから返信が届いています';
							$mail_body = $this->form->getTemplateContents($mail_arr, '_mail/consult_reply_to');
						}
						$mail_from = APP_CONST_SERVICE_EMAIL;
						$mail_from_name = APP_CONST_SITE_TITLE_S;
						$send_errmsg = '';
						if (Util::sendSmtpMail($mail_to, $mail_title, $mail_body, $mail_from, $mail_from_name, 'UTF-8', $send_errmsg) === false) {
							$this->logger->error("返信通知：メール送信に失敗しました。[To:${mail_to}, From:${mail_from}]\n".$send_errmsg);
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
	private function _validateReply()
	{
		$ret = true;
		$reply_opt = $this->form->get('reply_opt');
		if ($reply_opt != ConsultReplysDao::REPLY_OPT_NOADVICE) {
			$ret = $this->form->validate($this->form->getValidates(0));
		}
		return $ret;
	}

	/**
	 * 評価登録
	 */
	public function post_review()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$consult_id = $this->form->getInt('consult_id');
		if (empty($consult_id)) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
//			'security_token' => '',
			'is_finish' => 0,
			'fb_share' => array()
		);

		$userInfo = $this->getUserInfo();

		if ($this->_validateReview() === false)
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
				$consultsDao = new ConsultsDao($this->db);
				$consult = $consultsDao->getItem($consult_id);
				// 相談者のみが評価可能
				if (empty($consult) || $consult['consult_user_id'] != $userInfo['id']) {
					return $this->notfound();
				}

				$advicesDao = new AdvicesDao($this->db);
				$advice = $advicesDao->getItemByUserId($consult['advice_id'], $consult['advice_user_id']);
				if (empty($advice)) return $this->notfound();

				// 公開設定
				$consult_public_flag = $consult[ConsultsDao::COL_PUBLIC_FLAG];
				$review_public_flag = $consult_public_flag;
				// 非公開相談の場合
				if ($consult_public_flag == ConsultsDao::PUBLIC_FLAG_PRIVATE) {
					if ($this->form->get('review_public_flag') == ConsultReviewsDao::CONSULT_PUBLIC_FLAG_PUBLIC) {
						$review_public_flag = ConsultReviewsDao::CONSULT_PUBLIC_FLAG_PUBLIC;
					}
				}

				$this->db->beginTransaction();

				$consultReviewsDao = new ConsultReviewsDao($this->db);
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_CONSULT_REVIEW_USER_ID, $userInfo['id']);
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_ADVICE_ID, $consult['advice_id']);
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_ADVICE_USER_ID, $consult['advice_user_id']);
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_CONSULT_ID, $consult_id);
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_CONSULT_USER_ID, $consult['consult_user_id']);
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_CONSULT_PUBLIC_FLAG, $consult_public_flag);
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_REVIEW_PUBLIC_FLAG, $review_public_flag);
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_SECRET_FLAG, ($this->form->get('secret_flag')==1 ? 1 : 0));
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_REVIEW_SHARE, ($this->form->get('review_share')==1 ? 1 : 0));
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_EVALUATE_TYPE, $this->form->get('evaluate_type'));
				$consultReviewsDao->addValueStr(ConsultReviewsDao::COL_REVIEW_BODY, $this->form->get('review_body'));
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_CREATEDATE, Dao::DATE_NOW);
				$consultReviewsDao->addValue(ConsultReviewsDao::COL_LASTUPDATE, Dao::DATE_NOW);
				$consultReviewsDao->doInsert();

				$consult_review_id = $consultReviewsDao->getLastInsertId();

				$consultsDao->reset();
				$consultsDao->addValue(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_FINISH);
				$consultsDao->addValue(ConsultsDao::COL_REVIEW_STATE, ConsultsDao::REVIEW_STATE_WRITED);
				$consultsDao->addValue(ConsultsDao::COL_REVIEW_PUBLIC_FLAG, $review_public_flag);
				$consultsDao->addValue(ConsultsDao::COL_REPLYDATE, Dao::DATE_NOW);
				$consultsDao->addWhere(ConsultsDao::COL_CONSULT_ID, $consult_id);
				$consultsDao->doUpdate();

				$advicesDao->reset();
				$advicesDao->addValue(AdvicesDao::COL_REPLYDATE, Dao::DATE_NOW);
				$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $consult['advice_id']);
				$advicesDao->doUpdate();

				// 評価更新
				$userRanksDao = new UserRanksDao($this->db);
				$user_rank = $userRanksDao->getItem($consult['advice_user_id']);

				$evaluate_type = $this->form->get('evaluate_type');
				$evaluate_point = AppConst::$evaluatePoint[$evaluate_type];

				$userRanksDao->reset();

				$user_rank[UserRanksDao::COL_EVALUATE_TOTAL] = $user_rank[UserRanksDao::COL_EVALUATE_TOTAL] + $evaluate_point;
				$user_rank[UserRanksDao::COL_EVALUATE_NUM] = $user_rank[UserRanksDao::COL_EVALUATE_NUM] + 1;
				if ($user_rank[UserRanksDao::COL_EVALUATE_TOTAL] == 0) {
					$user_rank[UserRanksDao::COL_EVALUATE_AVE] = '0.0';
				} else {
					$user_rank[UserRanksDao::COL_EVALUATE_AVE] = number_format($user_rank[UserRanksDao::COL_EVALUATE_TOTAL] / $user_rank[UserRanksDao::COL_EVALUATE_NUM], 1);
				}
				$userRanksDao->addValue(UserRanksDao::COL_EVALUATE_TOTAL, $user_rank[UserRanksDao::COL_EVALUATE_TOTAL]);
				$userRanksDao->addValue(UserRanksDao::COL_EVALUATE_AVE, $user_rank[UserRanksDao::COL_EVALUATE_AVE]);
				$userRanksDao->addValue(UserRanksDao::COL_EVALUATE_NUM, $user_rank[UserRanksDao::COL_EVALUATE_NUM]);
				$col_evaluate_n = 'evaluate_'.$evaluate_type;
				$userRanksDao->addValue($col_evaluate_n, $col_evaluate_n.'+1');
				$userRanksDao->addWhere(UserRanksDao::COL_USER_ID, $user_rank['user_id']);
				$userRanksDao->doUpdate();

				// 通知
				NoticesDao::postReview($this->db, $consult['advice_user_id'], $userInfo['id'], $userInfo['nickname'], $advice['advice_id'], $advice['advice_title'], $consult_id, $this->form->get('review_body'));

				$this->db->commit();

				$consultReviewsDao->reset();
				$item = $consultReviewsDao->getItem($consult_review_id);

				$item['consult_id'] = $consult_id;

				$tpl_vars = array(
					'review' => $item,
					'review_user' => $userInfo,
					'userInfo' => $userInfo,
					'is_first' => false,
					'latest_reply_id' => $consult['latest_reply_id'],
					'REAL_URL' => $this->form->getSp('REAL_URL')
				);

				$json_data['html'] = $this->form->getTemplateContents($tpl_vars, '_parts/consult_thread/consult_thread_review_item');
				$json_data['result'] = 1;
				$json_data['is_finish'] = 1;
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
			}

			// メール通知
			if ($json_data['result'] == 1 && $consult['advice_user_id'] > 0)
			{
				$usersDao = new UsersDao($this->db);
				$user = $usersDao->getItem($consult['advice_user_id']);

				// Facebookシェア
				if ($this->form->getInt('is_fb_share') == 1 && $review_public_flag == ConsultReviewsDao::CONSULT_PUBLIC_FLAG_PUBLIC)
				{
					$json_data['fb_share'] = array(
						'message' => '[評価：'.AppConst::$evaluateType[$evaluate_type].']'.$this->form->get('review_body'),
						'link' => constant('app_site_real_url').'advice/'.$consult['advice_id'].'/'.$consult['consult_id'].'/',
						'name' => $user['nickname'].'さんを評価しました。- '.APP_CONST_SITE_TITLE_F,
						'description' => $userInfo['nickname'].'さんが'.$user['nickname'].'さんを評価しました。評価したアドバイスは「Adviner」上で閲覧することができます。',
						'picture' => constant('app_site_real_url').'img/fb_page.png'
					);
				}

				// 評価通知
				if ($user['consult_review_to'] == 1 && $user['email'] != '')
				{
					try
					{
						$mail_to = $user['email'];
						if ($advice['charge_flag'] == AdvicesDao::CHARGE_FLAG_CHARGE) {
							$click_url = constant('app_site_ssl_url').'advice/'.$consult['advice_id'].'/'.$consult['consult_id'].'/';
						} else {
							$click_url = constant('app_site_url').'advice/'.$consult['advice_id'].'/'.$consult['consult_id'].'/';
						}
						$mail_arr = array(
							'user' => $user,
							'userInfo' => $userInfo,
							'evaluate_text' => AppConst::$evaluateType[$evaluate_type],
							'review_body' => $this->form->get('review_body'),
							'click_url' => $click_url
						);
						$mail_title = '【'.APP_CONST_SITE_TITLE_S.'】'.$userInfo['nickname'].'さんから評価されました';
						$mail_body = $this->form->getTemplateContents($mail_arr, '_mail/consult_review_to');
						$mail_from = APP_CONST_SERVICE_EMAIL;
						$mail_from_name = APP_CONST_SITE_TITLE_S;
						$send_errmsg = '';
						if (Util::sendSmtpMail($mail_to, $mail_title, $mail_body, $mail_from, $mail_from_name, 'UTF-8', $send_errmsg) === false) {
							$this->logger->error("評価通知：メール送信に失敗しました。[To:${mail_to}, From:${mail_from}]\n".$send_errmsg);
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
	private function _validateReview()
	{
		$ret = $this->form->validate($this->form->getValidates(1));
		return $ret;
	}

	/**
	 * アドバイスください投稿
	 */
	public function post_please()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
//			'security_token' => '',
			'consult_id' => 0,
			'fb_share' => array()
		);

		$userInfo = $this->getUserInfo();

		if ($this->_validatePlease() === false)
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
				$nowdate = date("Y-m-d H:i:s");

				$this->db->beginTransaction();

				$ConsultsDao = new ConsultsDao($this->db);
				$ConsultsDao->addValue(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_WAIT);
				$ConsultsDao->addValue(ConsultsDao::COL_ADVICE_ID, 0);
				$ConsultsDao->addValue(ConsultsDao::COL_ADVICE_USER_ID, 0);
				$ConsultsDao->addValue(ConsultsDao::COL_CONSULT_USER_ID, $userInfo['id']);
				$ConsultsDao->addValue(ConsultsDao::COL_PUBLIC_FLAG, ConsultsDao::PUBLIC_FLAG_PUBLIC);
				$ConsultsDao->addValueStr(ConsultsDao::COL_CONSULT_BODY, $this->form->get('please_body'));
				$ConsultsDao->addValue(ConsultsDao::COL_PLEASE_FLAG, ConsultsDao::PLEASE_FLAG_ON);
				$ConsultsDao->addValueStr(ConsultsDao::COL_REPLYDATE, $nowdate);
				$ConsultsDao->addValueStr(ConsultsDao::COL_CREATEDATE, $nowdate);
				$ConsultsDao->addValueStr(ConsultsDao::COL_LASTUPDATE, $nowdate);
				$ConsultsDao->doInsert();

				$consult_id = $ConsultsDao->getLastInsertId();

				$this->db->commit();

				$json_data['consult_id'] = $consult_id;
				$json_data['result'] = 1;

				// Facebookシェア
				if ($this->form->getInt('is_fb_share') == 1)
				{
					$json_data['fb_share'] = array(
						'message' => '[相談]'.$this->form->get('please_body'),
						'link' => constant('app_site_real_url').'advice/consult/'.$consult_id.'/',
						//'name' => APP_CONST_SITE_TITLE_TOP,
						'description' => $userInfo['nickname'].'さんがアドバイスください！に投稿しました。アドバイスがあれば「Adviner」にアクセスしてアドバイスしてください。',
						'picture' => constant('app_site_real_url').'img/fb_page.png'
					);
				}
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$json_data['errmsg'] = 'システムエラーが発生しました。画面を更新してから再度実行してください。';
			}
		}

		return $this->jsonPage($json_data, false);
	}

	/**
	 * 入力チェック
	 */
	private function _validatePlease()
	{
		$ret = $this->form->validate($this->form->getValidates(2));
		return $ret;
	}
}
?>
