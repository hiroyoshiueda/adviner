<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('FollowsDao', 'dao');
Sp::import('AlzSearchWordsDao', 'dao');
Sp::import('FollowStream', 'libs');
Sp::import('ActionStream', 'libs');
/**
 * トップリストAPI(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiTopController extends BaseController
{
	const PAGE_LIMIT = 10;

	/**
	 * 相談窓口
	 */
	public function get_advice_list()
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
			$AdvicesDao = new AdvicesDao($this->db);
			$list = $AdvicesDao->getPopularPageList($total, $offset, $limit);

			$advice_ids = Util::arraySelectKey('advice_id', $list, true);
			$follow_set = array();

			if ($total > 0)
			{

				if ($this->checkUserAuth())
				{
					$FollowsDao = new FollowsDao($this->db);
					$follow_list = $FollowsDao->getAdviceList($userInfo['id'], $advice_ids);
					$follow_set = Util::arrayKeyData('follow_advice_id', $follow_list);
				}
				$this->setGoodButton($advice_ids);
			}

			$tpl_vars = array(
				'advice_list' => $list,
				'form' => array(
					'follow_set' => $follow_set
				),
				'good' => $this->form->getSp('good'),
				'userInfo' => $userInfo,
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL'),
				'is_top' => true
			);

			if ($offset > 0) $tpl_vars['is_top'] = false;

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, '_parts/advice_list');
			$json_data['result'] = 1;

			// 続きの有無
			if ($total > $offset + $limit) {
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
	 * フォロー中の相談
	 */
	public function get_follow()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$total = 0;
		$limit = self::PAGE_LIMIT;
		$pagenum = $this->form->getInt('pagenum');
		$offset = $pagenum * $limit;
		// 新着件数分を補正する
		$revise = $this->form->getInt('revise');
		if ($revise>0) $offset += $revise;

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'last_datetime' => '',
			'last_key' => ''
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$FollowsDao = new FollowsDao($this->db);
			$AdvicesDao = new AdvicesDao($this->db);
			$followData = $this->getLoadFollowData($FollowsDao, $AdvicesDao);

			$FollowStream = new FollowStream($this->db, $this->logger, $userInfo, $followData);
			$FollowStream->load();
			$feed_list = $FollowStream->getPageList($total, $offset, $limit);
			$FollowStream->relatedItems();

			$this->setGoodButton(array_merge($FollowStream->getAdviceIds(), array('0')));

			$tpl_vars = array(
				'feed_list' => $feed_list,
				'form' => array(
					'user_set' => $FollowStream->getUserSet(),
					'advice_set' => $FollowStream->getAdviceSet(),
					'consult_set' => $FollowStream->getConsultSet(),
					'reply_set' => $FollowStream->getReplySet(),
					'review_set' => $FollowStream->getReviewSet()
				),
				'good' => $this->form->getSp('good'),
				'userInfo' => $userInfo,
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL')
			);

			if (count($feed_list) > 0) {
				$json_data['last_datetime'] = $feed_list[0]['createdate'];
				$json_data['last_key'] = sprintf("%d_%d_%d_%d"
										, $feed_list[0]['advice_id']
										, $feed_list[0]['consult_id']
										, $feed_list[0]['consult_reply_id']
										, $feed_list[0]['consult_review_id']);
			}

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, '_parts/feed_list');
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
	 * フォロー中の相談の新着
	 */
	public function get_new_follow()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$last_datetime = $this->form->get('last_datetime');
		$last_key = $this->form->get('last_key');

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'last_datetime' => '',
			'last_key' => ''
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$FollowsDao = new FollowsDao($this->db);
			$AdvicesDao = new AdvicesDao($this->db);
			$followData = $this->getLoadFollowData($FollowsDao, $AdvicesDao);

			$FollowStream = new FollowStream($this->db, $this->logger, $userInfo, $followData);
			$FollowStream->load(false);
			$feed_list = $FollowStream->getNewList($last_datetime, $last_key);
			$FollowStream->relatedItems();

			$this->setGoodButton(array_merge($FollowStream->getAdviceIds(), array('0')));

			$tpl_vars = array(
				'feed_list' => $feed_list,
				'form' => array(
					'user_set' => $FollowStream->getUserSet(),
					'advice_set' => $FollowStream->getAdviceSet(),
					'consult_set' => $FollowStream->getConsultSet(),
					'reply_set' => $FollowStream->getReplySet(),
					'review_set' => $FollowStream->getReviewSet()
				),
				'good' => $this->form->getSp('good'),
				'userInfo' => $userInfo,
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL')
			);

			if (count($feed_list) > 0) {
				$json_data['last_datetime'] = $feed_list[0]['createdate'];
				$json_data['last_key'] = sprintf("%d_%d_%d_%d"
										, $feed_list[0]['advice_id']
										, $feed_list[0]['consult_id']
										, $feed_list[0]['consult_reply_id']
										, $feed_list[0]['consult_review_id']);
			}

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, '_parts/feed_list');
			$json_data['result'] = 1;
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
		}

		return $this->jsonPage($json_data, false);
	}

	/**
	 * すべての相談フィード
	 */
	public function get_all()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$total = 0;
		$limit = self::PAGE_LIMIT;
		$pagenum = $this->form->getInt('pagenum');
		$offset = $pagenum * $limit;
		// 新着件数分を補正する
		$revise = $this->form->getInt('revise');
		if ($revise>0) $offset += $revise;

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'last_datetime' => '',
			'last_key' => ''
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$FollowStream = new FollowStream($this->db, $this->logger, null, null, true);
			$FollowStream->load();
			$feed_list = $FollowStream->getPageList($total, $offset, $limit);
			$FollowStream->relatedItems();

			$this->setGoodButton(array_merge($FollowStream->getAdviceIds(), array('0')));

			$tpl_vars = array(
				'feed_list' => $feed_list,
				'form' => array(
					'user_set' => $FollowStream->getUserSet(),
					'advice_set' => $FollowStream->getAdviceSet(),
					'consult_set' => $FollowStream->getConsultSet(),
					'reply_set' => $FollowStream->getReplySet(),
					'review_set' => $FollowStream->getReviewSet()
				),
				'good' => $this->form->getSp('good'),
				'userInfo' => $userInfo,
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL')
			);

			if (count($feed_list) > 0) {
				$json_data['last_datetime'] = $feed_list[0]['createdate'];
				$json_data['last_key'] = sprintf("%d_%d_%d_%d"
										, $feed_list[0]['advice_id']
										, $feed_list[0]['consult_id']
										, $feed_list[0]['consult_reply_id']
										, $feed_list[0]['consult_review_id']);
			}

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, '_parts/feed_list');
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
	 * すべての相談の新着
	 */
	public function get_new_all()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$last_datetime = $this->form->get('last_datetime');
		$last_key = $this->form->get('last_key');

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'last_datetime' => '',
			'last_key' => ''
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$FollowStream = new FollowStream($this->db, $this->logger, null, null, true);
			$FollowStream->load(false);
			$feed_list = $FollowStream->getNewList($last_datetime, $last_key);
			$FollowStream->relatedItems();

			$this->setGoodButton(array_merge($FollowStream->getAdviceIds(), array('0')));

			$tpl_vars = array(
				'feed_list' => $feed_list,
				'form' => array(
					'user_set' => $FollowStream->getUserSet(),
					'advice_set' => $FollowStream->getAdviceSet(),
					'consult_set' => $FollowStream->getConsultSet(),
					'reply_set' => $FollowStream->getReplySet(),
					'review_set' => $FollowStream->getReviewSet()
				),
				'good' => $this->form->getSp('good'),
				'userInfo' => $userInfo,
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL')
			);

			if (count($feed_list) > 0) {
				$json_data['last_datetime'] = $feed_list[0]['createdate'];
				$json_data['last_key'] = sprintf("%d_%d_%d_%d"
										, $feed_list[0]['advice_id']
										, $feed_list[0]['consult_id']
										, $feed_list[0]['consult_reply_id']
										, $feed_list[0]['consult_review_id']);
			}

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, '_parts/feed_list');
			$json_data['result'] = 1;
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
		}

		return $this->jsonPage($json_data, false);
	}

	public function get_please_advice()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$total = 0;
		$limit = self::PAGE_LIMIT;
		$pagenum = $this->form->getInt('pagenum');
		$offset = $pagenum * $limit;
		// 新着件数分を補正する
		$revise = $this->form->getInt('revise');
		if ($revise>0) $offset += $revise;

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'last_key' => ''
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$ConsultsDao = new ConsultsDao($this->db);
			$consult_list = $ConsultsDao->getPleaseAdvicePageList($total, $offset, $limit);

			$consult_ids = Util::arraySelectKey('consult_id', $consult_list);
			$this->setGoodButton(0, $consult_ids);

			$tpl_vars = array(
				'consult_list' => $consult_list,
				'userInfo' => $userInfo,
				'good' => $this->form->getSp('good'),
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL'),
				'is_top' => false
			);

			if ($offset == 0) {
				$tpl_vars['is_top'] = true;
			}

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/list/api_list_get_please_advice');
			$json_data['result'] = 1;

			// 続きがあるか
			if ($total > $limit + $offset) {
				$json_data['more'] = 1;
			}

			if (count($consult_list) > 0) {
				$json_data['last_key'] = $consult_list[0]['consult_id'];
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
		}

		return $this->jsonPage($json_data, false);
	}

	public function get_new_please_advice()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$total = 0;
		$limit = 100;
		$offset = 0;
		$last_consult_id = $this->form->getInt('last_key');

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'last_key' => '',
			'total' => 0
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$ConsultsDao = new ConsultsDao($this->db);
			if ($last_consult_id > 0)
			{
				$ConsultsDao->addWhere('c.'.ConsultsDao::COL_CONSULT_ID, $last_consult_id, '>');
			}
			$consult_list = $ConsultsDao->getPleaseAdvicePageList($total, $offset, $limit);

			$consult_ids = Util::arraySelectKey('consult_id', $consult_list);
			$this->setGoodButton(0, $consult_ids);

			$tpl_vars = array(
				'consult_list' => $consult_list,
				'userInfo' => $userInfo,
				'good' => $this->form->getSp('good'),
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL')
			);

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/list/api_list_get_please_advice');
			$json_data['result'] = 1;

			// 続きがあるか
			if ($total > $limit + $offset) {
				$json_data['more'] = 1;
			}

			$json_data['total'] = count($consult_list);
			if ($json_data['total'] > 0) {
				$json_data['last_key'] = $consult_list[0]['consult_id'];
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
		}

		return $this->jsonPage($json_data, false);
	}

	public function get_action()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$total = 0;
		$limit = self::PAGE_LIMIT;
		$pagenum = $this->form->getInt('pagenum');
		$offset = $pagenum * $limit;
		// 新着件数分を補正する
		$revise = $this->form->getInt('revise');
		if ($revise>0) $offset += $revise;

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => ''
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$ActionStream = new ActionStream($this->db, $this->logger, $userInfo);
			$consult_list = $ActionStream->getPageList($total, $offset, $limit);
			$ActionStream->relatedItems();

			$this->setGoodButton($ActionStream->getAdviceIds());

			$tpl_vars = array(
				'consult_list' => $consult_list,
				'form' => array(
					'user_set' => $ActionStream->getUserSet(),
					'advice_set' => $ActionStream->getAdviceSet(),
					'reply_set' => $ActionStream->getReplySet(),
					'review_set' => $ActionStream->getReviewSet()
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

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/list/api_list_get_action');
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

	public function get_search()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$this->form->set('limit', self::PAGE_LIMIT);

		$total = 0;
		$limit = $this->form->get('limit');
		$pagenum = $this->form->getInt('pagenum');
		$offset = $pagenum * $limit;

		$q = $this->form->get('q');
		$target = $this->form->get('target');
		if (empty($target)) return $this->notfound();
		$main_category_id = $this->form->getInt('main_category_id');

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => ''
		);

		$words = array();

		if ($q != '')
		{
			$q = str_replace(array("\n","\r","\t","\0","\x0B"), '', $q);
			$q = mb_substr($q, 0, 100);
			$q_arr = mb_split("[ 　]+", $q);
			$q_max = count($q_arr);
			for ($i=0; $i<$q_max; $i++) {
				$q_str = trim($q_arr[$i]);
				if ($q_str != '') {
					$words[] = $q_str;
					// 複数キーワードは3つまで
					if (count($words)>=3) break;
				}
			}
		}

		if (count($words) == 0)
		{
			$json_data['errmsg'] = '検索に有効なキーワードを入力してください。';
			return $this->jsonPage($json_data, false);
		}

		try
		{
			$alzSearchWordsDao = new AlzSearchWordsDao($this->db);
			$alzSearchWordsDao->register($q);

			$advicesDao = new AdvicesDao($this->db);
			$category_ids = $main_category_id > 0 ? array($main_category_id) : null;
			$list = $advicesDao->getPopularPageList($total, $offset, $limit, $category_ids, $words);
			$json_data['htitle'] = '検索結果';

			$category_set = $this->getLoadCategorySet(new CategorysDao($this->db));

			$tpl_vars = array(
				'advice_list' => $list,
				'form' => array('category_set' => $category_set),
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'is_top' => false
			);

			if ($offset == 0) {
				$tpl_vars['is_top'] = true;
			}

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, '_parts/advice_list');
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
