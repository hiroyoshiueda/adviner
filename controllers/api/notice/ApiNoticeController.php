<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('NoticesDao', 'dao');
Sp::import('UsersDao', 'dao');
/**
 * ヘッダー・通知API(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiNoticeController extends BaseController
{
	const PAGE_LIMIT = 10;

	public function get_list()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$total = 0;
		$limit = self::PAGE_LIMIT;
		$offset = 0;

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => ''
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$NoticesDao = new NoticesDao($this->db);
			$list = $NoticesDao->getPageList($total, 0, $limit, $userInfo['id']);

			if (count($list) > 0)
			{
				$advice_ids = Util::arraySelectKey(NoticesDao::COL_ADVICE_ID, $list, true);

				$AdvicesDao = new AdvicesDao($this->db);
				$advice_list = $AdvicesDao->getAdviceList($advice_ids, -1, -1);
				$advice_set  = Util::arrayKeyData('advice_id', $advice_list);

				$user_ids = Util::arraySelectKey(NoticesDao::COL_FROM_USER_ID, $list, true);

				$UsersDao = new UsersDao($this->db);
				$user_list = $UsersDao->getUserList($user_ids, -1, -1);
				$user_set = Util::arrayKeyData('user_id', $user_list);

				$NoticesDao->reads($userInfo['id'], null);
			}

			$tpl_vars = array(
				'notice_list' => $list,
				'form' => array(
					'advice_set' => $advice_set,
					'user_set' => $user_set
				),
//				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'is_top' => false
			);

			if ($offset == 0) {
				$tpl_vars['is_top'] = true;
			}

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/notice/api_notice_get_list');
			$json_data['result'] = 1;

			// 続きがあるか
			if ($total > $limit + $offset) {
				$json_data['more'] = 1;
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
		}

		return $this->jsonPage($json_data, false);
	}

	/**
	 * 未読通知件数の取得
	 */
	public function get_unread()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'total' => 0,
			'unread' => 0
		);

		$userInfo = $this->getUserInfo();

		list($total, $unread) = $this->_get_unread_data();

		$json_data['html'] = '<span>'.$unread.'</span>';
		$json_data['result'] = 1;
		$json_data['total'] = $total;
		$json_data['unread'] = $unread;

		return $this->jsonPage($json_data, false);
	}

	private function _get_unread_data()
	{
		$userInfo = $this->getUserInfo();

		// 未読通知
		$NoticesDao = new NoticesDao($this->db);
		$unread = $NoticesDao->getUnreadTotal($userInfo['id']);

		$ConsultsDao = new ConsultsDao($this->db);
		$ConsultsDao->addSelectCount(ConsultsDao::COL_CONSULT_ID, 'total');
		$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_USER_ID, $userInfo['id']);
		$ConsultsDao->addWhere(ConsultsDao::COL_REVIEW_STATE, ConsultsDao::REVIEW_STATE_WAIT);
		$wait_review = $ConsultsDao->selectId();

		$unread_total = $unread + $wait_review;

		return array($unread_total, $unread);
	}
}
?>
