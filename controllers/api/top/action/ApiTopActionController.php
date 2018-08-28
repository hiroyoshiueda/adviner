<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('UsersDao', 'dao');
/**
 * トップ・あなたの相談リストAPI(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiTopActionController extends BaseController
{
	const PAGE_LIMIT = 10;

	/**
	 * あなたの相談リスト
	 */
	public function get_list()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$total = 0;
		$limit = self::PAGE_LIMIT;
		$pagenum = $this->form->getInt('pagenum');
		$offset = $pagenum * $limit;

		$json_data = array(
			'html' => '',
			'more' => 0,
			'result' => 0,
			'errmsg' => ''
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$ConsultsDao = new ConsultsDao($this->db);
			$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_DURING);
			$wh = '('.ConsultsDao::COL_ADVICE_USER_ID.'='.$userInfo['id']
				. ' OR '.ConsultsDao::COL_CONSULT_USER_ID.'='.$userInfo['id'].')';
			$ConsultsDao->addWhere('', $wh);
			$ConsultsDao->addWhere(ConsultsDao::COL_DELETE_FLAG, ConsultsDao::DELETE_FLAG_ON);
			$ConsultsDao->addWhere(ConsultsDao::COL_DISPLAY_FLAG, ConsultsDao::DISPLAY_FLAG_ON);
			$ConsultsDao->addOrder(ConsultsDao::COL_REPLYDATE, 'DESC');
			$list = $ConsultsDao->selectPage($offset, $limit, $total);

			$user_set   = array();
			$advice_set = array();
			$reply_set  = array();
			$review_set = array();

			if ($total > 0)
			{
				$consult_ids = Util::arraySelectKey('consult_id', $list);

				$ConsultReplysDao = new ConsultReplysDao($this->db);
				$reply_list = $ConsultReplysDao->getListByConsultIds($consult_ids);
				$reply_set  = Util::arrayKeyData('consult_id,consult_reply_id', $reply_list);

				$ConsultReviewsDao = new ConsultReviewsDao($this->db);
				$review_list = $ConsultReviewsDao->getListByConsultIds($consult_ids);
				$review_set  = Util::arrayKeyData('consult_id,consult_review_id', $review_list);

				$advice_ids = Util::arraySelectKey('advice_id', $list, true);

				$AdvicesDao = new AdvicesDao($this->db);
				$advice_list = $AdvicesDao->getAdviceList($advice_ids);
				$advice_set  = Util::arrayKeyData('advice_id', $advice_list);

				$advice_user_ids = Util::arraySelectKey('advice_user_id', $list, true);
				$consult_user_ids = Util::arraySelectKey('consult_user_id', $list, true);
				$user_ids = array_merge($advice_user_ids, $consult_user_ids);

				$UsersDao = new UsersDao($this->db);
				$user_list = $UsersDao->getUserList($user_ids);
				$user_set = Util::arrayKeyData('user_id', $user_list);

				$this->setGoodButton($advice_ids);
			}

			$tpl_vars = array(
				'action_list' => $list,
				'form' => array(
					'user_set' => $user_set,
					'advice_set' => $advice_set,
					'reply_set' => $reply_set,
					'review_set' => $review_set
				),
				'good' => $this->form->getSp('good'),
				'userInfo' => $userInfo,
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys, 'evaluateType' => AppConst::$evaluateType),
				'REAL_URL' => $this->form->getSp('REAL_URL'),
				'is_top' => false
			);

			if ($offset == 0) {
				$tpl_vars['is_top'] = true;
			}

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/top/action/api_top_action_get_list');
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
}
?>
