<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('AdviceRanksDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('NoticesDao', 'dao');
Sp::import('FollowsDao', 'dao');
/**
 * 相談詳細
 * @author Hiroyoshi
 */
class AdviceController extends BaseController
{
	/**
	 * 詳細＆フォーム
	 */
	public function index()
	{
		$advice_id = $this->form->getInt('advice_id');
		if (empty($advice_id)) return $this->notfound();

		$total = 0;
		$limit = 20;
		$offset = $this->form->getInt('offset');

		if ($this->_setBaseData(false) === false) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$advice = $this->form->get('advice');
		$user = $this->form->get('user');

		// 承認済み or 停止中のみ有効
		if ($advice['advice_status'] == AdvicesDao::ADVICE_STATUS_OK || $advice['advice_status'] == AdvicesDao::ADVICE_STATUS_STOP)
		{
			$consultsDao = new ConsultsDao($this->db);
			$consult_list = $consultsDao->getPageListOfAdvice($total, $offset, $limit, $advice_id);
			$this->form->set('consult_list', $consult_list);
			$this->form->set('consult_total', $total);
			$this->form->set('consult_limit', $limit);

			$user_list = array();
			$reply_list = array();
			$review_list = array();

			if (count($consult_list)>0)
			{
				$consult_ids = Util::arraySelectKey('consult_id', $consult_list);

				$ConsultReplysDao = new ConsultReplysDao($this->db);
				$reply_list = $ConsultReplysDao->getListByConsultIds($consult_ids);

				$ConsultReviewsDao = new ConsultReviewsDao($this->db);
				$review_list = $ConsultReviewsDao->getListByConsultIds($consult_ids);

				$consult_user_ids = Util::arraySelectKey('consult_user_id', $consult_list);
				$consult_user_ids[] = $advice['advice_user_id'];

				$UsersDao = new UsersDao($this->db);
				$user_list = $UsersDao->getUserList($consult_user_ids);
			}

			$this->form->set('user_set', Util::arrayKeyData('user_id', $user_list));
			$this->form->set('reply_set', Util::arrayKeyData('consult_id,consult_reply_id', $reply_list));
			$this->form->set('review_set', Util::arrayKeyData('consult_id,consult_review_id', $review_list));

			// PVカウントアップ
//			if ($this->isNotUserAgent() === false && $this->isNotIp() === false && $this->setUniqCount($advice_id, APP_CONST_UNIQ_ADVICE_COOKIE_NAME) !== false)
//			if ($this->isNotUserAgent() === false && $this->setUniqCount($advice_id, APP_CONST_UNIQ_ADVICE_COOKIE_NAME) !== false)
			if ($this->isNotUserAgent() === false)
			{
				$advicesDao = new AdvicesDao($this->db);
				$advicesDao->updateCountPv($advice_id);

				$adviceRanksDao = new AdviceRanksDao($this->db);
				$adviceRanksDao->updateCountPv($advice_id);

				$advice['pv_total'] += 1;
				$advice['pv_today'] += 1;
				$this->form->set('advice', $advice);
			}

			// 同じカテゴリーの他の相談窓口
			$this->setSidePopularListByCategory(array($advice['category_id']), $advice_id);
		}
		else
		{
			// 承認待ちは404を返す
			$this->resp->setStatus(SpResponse::SC_NOT_FOUND);
		}

		$userRanksDao = new UserRanksDao($this->db);
		$this->form->set('user_rank', $userRanksDao->getItem($user['user_id']));

		$this->form->set('htitle', $advice['advice_title']);
		$this->setTitle('[相談窓口]'.$this->form->get('htitle'), $user['nickname']);
		$this->setDescription($user['nickname'].'の相談窓口です。'.$advice['advice_body']);

		$this->setGoodButton($advice_id);
		$this->setSocialButton();

		return $this->forward('advice/advice_index', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 相談フォーム
	 */
	public function entry()
	{
		$advice_id = $this->form->getInt('advice_id');
		if (empty($advice_id)) return $this->notfound();

		if ($this->_setBaseData($advice_id) === false) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$advice = $this->form->get('advice');
		$user = $this->form->get('user');

		$userRanksDao = new UserRanksDao($this->db);
		$this->form->set('user_rank', $userRanksDao->getItem($user['user_id']));

		$this->form->set('htitle', '相談する');
		$this->setTitle($this->form->get('htitle'), $advice['advice_title'].' - '.$user['nickname']);

		$this->form->setScript($this->form->get('JS_URL').'/js/adviner.onload.js');

		$this->createSecurityCode();

		return $this->forward('advice/advice_entry', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 確認
	 */
	public function confirm()
	{
		if ($this->checkUserAuth() === false) return $this->notfound();

		$advice_id = $this->form->getInt('advice_id');
		if (empty($advice_id)) return $this->notfound();

		if ($this->_validate() === false) {
			$this->form->set('errors', $this->form->getValidateErrors());
			return $this->entry();
		}

		if ($this->_setBaseData() === false) return $this->notfound();

		$user = $this->form->get('user');

		$userRanksDao = new UserRanksDao($this->db);
		$this->form->set('user_rank', $userRanksDao->getItem($user['user_id']));

		$this->form->setParameterForm('consult_body');
		$this->form->setParameterForm('agree');
		//$this->form->setParameterForm('public_flag');

		$this->form->set('htitle', '相談内容の確認');
		$this->setTitle($this->form->get('htitle'));

		$this->createSecurityCode();

		$this->resp->noCache();

		return $this->forward('advice/advice_confirm', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 登録完了
	 */
	public function complete()
	{
		if ($this->checkUserAuth() === false) return $this->notfound();

		$advice_id = $this->form->getInt('advice_id');
		if (empty($advice_id)) return $this->notfound();

		if ($this->checkSecurityCode() === false)
		{
			return $this->errorPage(self::ERROR_PAGE_MESSAGE5);
		}
		else if ($this->_validate() === false)
		{
			$this->form->set('errors', $this->form->getValidateErrors());
		}
		else if ($this->_setBaseData() === false)
		{
			return $this->notfound();
		}
		else
		{
			$userInfo = $this->getUserInfo();

			$advice = $this->form->get('advice');
			$user = $this->form->get('user');

			try
			{
				$nowdate = date("Y-m-d H:i:s");
				// 7日後に自動終了
				$enddate = date("Y-m-d", time() + 604800) . ' 23:59:59';

				$this->db->beginTransaction();

				$ConsultsDao = new ConsultsDao($this->db);
				$ConsultsDao->addValue(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_DURING);
				$ConsultsDao->addValue(ConsultsDao::COL_ADVICE_ID, $advice['advice_id']);
				$ConsultsDao->addValue(ConsultsDao::COL_ADVICE_USER_ID, $advice['advice_user_id']);
				$ConsultsDao->addValue(ConsultsDao::COL_CONSULT_USER_ID, $userInfo['id']);
				$ConsultsDao->addValueStr(ConsultsDao::COL_CONSULT_BODY, $this->form->get('consult_body'));
				// 有料
				if ($advice['charge_flag'] == AdvicesDao::CHARGE_FLAG_CHARGE)
				{
					$ConsultsDao->addValue(ConsultsDao::COL_PUBLIC_FLAG, ConsultsDao::PUBLIC_FLAG_PRIVATE);
					$ConsultsDao->addValue(ConsultsDao::COL_ADVICE_CHARGE_FLAG, $advice['charge_flag']);
					$ConsultsDao->addValue(ConsultsDao::COL_ADVICE_CHARGE_PRICE, $advice['charge_price']);
				}
				else
				{
					$ConsultsDao->addValue(ConsultsDao::COL_PUBLIC_FLAG, ConsultsDao::PUBLIC_FLAG_PUBLIC);
					//$ConsultsDao->addValue(ConsultsDao::COL_PUBLIC_FLAG, ($this->form->get('public_flag')==1 ? ConsultsDao::PUBLIC_FLAG_PRIVATE : ConsultsDao::PUBLIC_FLAG_PUBLIC));
				}
				$ConsultsDao->addValueStr(ConsultsDao::COL_FINISHDATE, $enddate);
				$ConsultsDao->addValueStr(ConsultsDao::COL_REPLYDATE,  $nowdate);
				$ConsultsDao->addValueStr(ConsultsDao::COL_CREATEDATE, $nowdate);
				$ConsultsDao->addValueStr(ConsultsDao::COL_LASTUPDATE, $nowdate);
				$ConsultsDao->doInsert();

				$consult_id = $ConsultsDao->getLastInsertId();

				$AdvicesDao = new AdvicesDao($this->db);
				$AdvicesDao->addValueStr(AdvicesDao::COL_REPLYDATE, $nowdate);
				$AdvicesDao->addValue(AdvicesDao::COL_CONSULT_TOTAL, AdvicesDao::COL_CONSULT_TOTAL.'+1');
				$AdvicesDao->addValue(AdvicesDao::COL_CONSULT_TODAY, AdvicesDao::COL_CONSULT_TODAY.'+1');
				$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $advice['advice_id']);
				$AdvicesDao->doUpdate();

				// 相談数をUP
				$AdviceRanksDao = new AdviceRanksDao($this->db);
				$AdviceRanksDao->updateCountConsult($advice['advice_id']);

				$UserRanksDao = new UserRanksDao($this->db);
				$UserRanksDao->updateCountConsult($advice['advice_user_id']);

				// 通知
				NoticesDao::postConsult($this->db, $advice['advice_user_id'], $userInfo['id'], $userInfo['nickname'], $advice['advice_id'], $advice['advice_title'], $consult_id, $this->form->get('consult_body'));

				$this->db->commit();

				// アドバイザーへ通知
				if ($user['consult_mail_to'] == 1 && $user['email'] != '')
				{
					$mail_to = $user['email'];
					$mail_arr = array(
						'user' => $user,
						'userInfo' => $userInfo,
						'consult_body' => $this->form->get('consult_body'),
						'createdate' => $nowdate
					);
					// 有料アドバイス
					if ($advice['charge_flag'] == AdvicesDao::CHARGE_FLAG_CHARGE) {
						$mail_title = '【'.APP_CONST_SITE_TITLE_S.'】'.$userInfo['nickname'].'さんから有料相談されました';
						$mail_arr['click_url'] = constant('app_site_ssl_url').'advice/'.$advice_id.'/'.$consult_id.'/';
						$mail_body = $this->form->getTemplateContents($mail_arr, '_mail/consult_charge_mail_to');
					} else {
						$mail_title = '【'.APP_CONST_SITE_TITLE_S.'】'.$userInfo['nickname'].'さんから相談されました';
						$mail_arr['click_url'] = constant('app_site_url').'advice/'.$advice_id.'/'.$consult_id.'/';
						$mail_body = $this->form->getTemplateContents($mail_arr, '_mail/consult_mail_to');
					}
					$mail_from = APP_CONST_SERVICE_EMAIL;
					$mail_from_name = APP_CONST_SITE_TITLE_S;
					$send_errmsg = '';
					if (Util::sendSmtpMail($mail_to, $mail_title, $mail_body, $mail_from, $mail_from_name, 'UTF-8', $send_errmsg) === false) {
						$this->logger->error("相談通知：メール送信に失敗しました。[To:${mail_to}, From:${mail_from}]\n".$send_errmsg);
					}
				}

				return $this->resp->sendRedirect('/advice/'.$advice_id.'/'.$consult_id.'/?post_consult=true');
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$this->db->rollback();
			}
		}

		return $this->entry();
	}

	private function _setBaseData($is_status=true)
	{
		$advicesDao = new AdvicesDao($this->db);
		$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $this->form->getInt('advice_id'));
		$advicesDao->addWhere(AdvicesDao::COL_DELETE_FLAG, AdvicesDao::DELETE_FLAG_ON);
		$advice = $advicesDao->selectRow();
		if (empty($advice)) return false;
		if ($is_status && $advice[AdvicesDao::COL_ADVICE_STATUS] != AdvicesDao::ADVICE_STATUS_OK) return false;
		$this->form->set('advice', $advice);

		$usersDao = new UsersDao($this->db);
		$user = $usersDao->getItem($advice['advice_user_id'], UsersDao::STATUS_REGULAR);
		if (empty($user)) return false;
		$this->form->set('user', $user);

		return true;
	}

	/**
	 * 入力チェック
	 */
	private function _validate()
	{
		$ret = $this->form->validate($this->form->getValidates(0));
		return $ret;
	}
}
?>
