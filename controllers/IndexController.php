<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('FollowsDao', 'dao');
Sp::import('AlzSearchTagsDao', 'dao');
Sp::import('AlzSearchWordsDao', 'dao');
/**
 * INDEX(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class IndexController extends BaseController
{
	const CACHE_STORE_PICKUP_LIST = 'cache_store_pickup_list';

	const PAGE_LIMIT = 10;

	/**
	 * トップ表示
	 */
	public function index()
	{
		if ($this->checkUserAuth())
		{
			return $this->_home();
		}
		else
		{
			return $this->_index();
		}
	}

	/**
	 * 未ログイントップ表示
	 */
	private function _index()
	{
		$total = 0;

		$advicesDao = new AdvicesDao($this->db);
		$this->form->set('pickup_list', $advicesDao->getPopularPageList($total, 0, 10));

		$this->form->set('rememberme', ($this->form->getCookieInt('rememberme')==1) ? '1' : '0');

		$this->form->setScript($this->form->get('JS_URL').'/js/index.js', '', constant('APP_CONST_JS_VER'));

		return $this->forward('index', APP_CONST_MAIN_FRAME);
	}

	/**
	 * ログイン後トップ表示
	 */
	private function _home()
	{
		$this->form->set('limit', self::PAGE_LIMIT);

		$total = 0;
		$limit = $this->form->get('limit');
		$offset = $this->form->getInt('offset');

		// admin用
		if ($this->form->get('admin_clear_load_category_set') == 'xxx') {
			// http://adviner.com/?admin_clear_load_category_set=xxx
			$this->clearLoadCategorySet();
		} else if ($this->form->get('admin_clear_load_pickup_list') == 'xxx') {
			// http://adviner.com/?admin_clear_load_pickup_list=xxx
			$this->clearLoadPickupList();
		}

		$userInfo = $this->getUserInfo();

//		$htitle = '人気の相談窓口';

		$advicesDao = new AdvicesDao($this->db);

		// ?offset=20への対応
		// 人気の相談窓口
		if ($offset > 0) {
			$this->form->set('list', $advicesDao->getPopularPageList($total, $offset, $limit));
			$this->form->set('list_total', $total);
			$this->form->set('list_limit', $limit);
			$this->form->set('list_offset', $offset);
		}

		$ConsultsDao = new ConsultsDao($this->db);
		$ConsultsDao->addSelectCount(ConsultsDao::COL_CONSULT_ID, 'total');
		$wh = sprintf("(%s=%d OR %s=%d)", ConsultsDao::COL_ADVICE_USER_ID, $userInfo['id'], ConsultsDao::COL_CONSULT_USER_ID, $userInfo['id']);
		$ConsultsDao->addWhere('', $wh);
		$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_DURING);
		$ConsultsDao->addWhere(ConsultsDao::COL_DISPLAY_FLAG, ConsultsDao::DISPLAY_FLAG_ON);
		$ConsultsDao->addWhere(ConsultsDao::COL_DELETE_FLAG, ConsultsDao::DELETE_FLAG_ON);
		$this->form->set('action_total', $ConsultsDao->selectId());

		$UserRanksDao = new UserRanksDao($this->db);
		$this->setLoadUserRank($UserRanksDao);

		// サイド用新着相談窓口の読み込み
		$this->setSideRecentList();

		// おすすめ相談窓口
		$this->setRecommendList();

		$this->setTitle('');

//		$this->form->setStyleSheet($this->form->get('JS_URL').'/css/');

		$this->form->setScript('https://www.paypalobjects.com/js/external/dg.js');
		$this->form->setScript($this->form->get('JS_URL').'/js/jquery/jquery.ba-hashchange.min.js');
		$this->form->setScript($this->form->get('JS_URL').'/js/jquery/jquery.DOMWindow.js');
		$this->form->setScript($this->form->get('JS_URL').'/js/jquery/jquery.autosize-min.js');
		$this->form->setScript($this->form->get('JS_URL').'/js/top.js', '', constant('APP_CONST_JS_VER'));

		$this->setGoodButton();
		$this->setSocialButton(array('fb_like', 'twitter', 'g_plusone', 'hatena'));

		$this->createSecurityCode();

		return $this->forward('home', APP_CONST_MAIN_FRAME);
	}

	/**
	 * おすすめ相談窓口
	 */
	private function setRecommendList()
	{
		$recommend_ids = array();
		// 相談ID、ユーザID
		$recommend_map = array(
			array(6, 9),
			array(243, 1151),
			array(242, 1148),
			array(241, 1141),
			array(240, 1126),
			array(239, 1133),
			array(252, 1148),
			array(253, 1148)
		);
		shuffle($recommend_map);
		foreach ($recommend_map as $recommends) {
			$aid = $recommends[0];
			$uid = $recommends[1];
			if (isset($recommend_ids[$uid]) === false) {
				$recommend_ids[$uid] = $aid;
				if (count($recommend_ids) == 4) break;
			}
		}

		$AdvicesDao = new AdvicesDao($this->db);
		$this->form->set('recommend_set', $AdvicesDao->getRecommendSet($recommend_ids, 4));
		$this->form->set('recommend_ids', $recommend_ids);
		return;
	}

//	public function admin_userauth()
//	{
//		$id = $this->form->getInt('uuid', 0);
//		if (empty($id)) return $this->notfound();
//		$dao = new UsersDao($this->db);
//		$dao->addWhere(UsersDao::COL_USER_ID, $id);
//		$user = $dao->selectRow();
//		var_dump($user);
//		$this->setUserInfo($user);
//		return $this->resp->sendRedirect('/');
//	}

//	/**
//	 * PickUp用リスト1時間ごとに更新
//	 * @param AdvicesDao $advicesDao
//	 * @return NULL or array
//	 */
//	private function getLoadPickupList(&$advicesDao)
//	{
//		$is_apc = function_exists('apc_store');
//		$pickup_list = null;
//		if ($is_apc && APP_APC_CACHE)
//		{
//			$pickup_list = apc_fetch(self::CACHE_STORE_PICKUP_LIST);
//		}
//		if (empty($pickup_list))
//		{
//			$list = array();
//			$advicesDao->reset();
//			$advicesDao->addWhereIn('a.'.AdvicesDao::COL_ADVICE_ID, AppConst::$pickupAdviceIds);
//			$pickup_list = $advicesDao->getPopularList(4);
//			if ($is_apc && APP_APC_CACHE) apc_store(self::CACHE_STORE_PICKUP_LIST, $pickup_list, 3600);
//		}
//		return $pickup_list;
//	}

	private function clearLoadPickupList()
	{
		@apc_delete(self::CACHE_STORE_PICKUP_LIST);
	}

	/**
	 * ログイン処理
	 */
	public function login()
	{
		return $this->loginPage();
	}

	/**
	 * ログアウト処理
	 */
	public function logout()
	{
		$this->deleteUserInfo();
		$this->resp->sessionEnd();
		$rd_url = $this->form->get('rd_url', '/');
		if ($rd_url != '' && !preg_match("|^https?://|i", $rd_url)) $rd_url = '/';
		return $this->resp->sendRedirect($rd_url);
	}

	/**
	 * サイトマップ
	 */
	public function sitemap()
	{
		if (APP_IS_PUBLIC === false) return $this->notfound();

		$list = array();

		$ndt = date('c');

		$this->_setSitemapList($list, '', $ndt);
		$this->_setSitemapList($list, 'service/', $ndt, 'weekly', '0.5');
		$this->_setSitemapList($list, 'service/charge', $ndt, 'weekly', '0.5');
		$this->_setSitemapList($list, 'help/', $ndt, 'weekly', '0.5');


		$advicesDao = new AdvicesDao($this->db);
		$advicesDao->addSelect(AdvicesDao::COL_ADVICE_ID);
		$advice_list = $advicesDao->getPopularList(200);
		if (count($advice_list)>0) {
			foreach ($advice_list as $d) {
				$this->_setSitemapList($list, 'advice/'.$d['advice_id'].'/', $ndt, 'daily', '1.0');
			}
		}

		$ConsultsDao = new ConsultsDao($this->db);
		$ConsultsDao->addSelect(ConsultsDao::COL_CONSULT_ID);
		$ConsultsDao->addSelect(ConsultsDao::COL_ADVICE_ID);
		$ConsultsDao->addWhere(ConsultsDao::COL_LATEST_REPLY_ID, 0, '>');
		$ConsultsDao->addWhere(ConsultsDao::COL_PUBLIC_FLAG, ConsultsDao::PUBLIC_FLAG_PUBLIC);
		$ConsultsDao->addWhere(ConsultsDao::COL_ADVICE_CHARGE_FLAG, ConsultsDao::CHARGE_FLAG_FREE);
		$consult_list = $ConsultsDao->getList(200);
		if (count($consult_list)>0) {
			foreach ($consult_list as $d) {
				$this->_setSitemapList($list, 'advice/'.$d['advice_id'].'/'.$d['consult_id'].'/', $ndt, 'daily', '0.8');
			}
		}

		$ConsultReviewsDao = new ConsultReviewsDao($this->db);
		$ConsultReviewsDao->addSelect(ConsultReviewsDao::COL_CONSULT_REVIEW_ID);
		$ConsultReviewsDao->addWhere(ConsultReviewsDao::COL_CONSULT_PUBLIC_FLAG, ConsultReviewsDao::CONSULT_PUBLIC_FLAG_PUBLIC);
		$ConsultReviewsDao->addWhere(ConsultReviewsDao::COL_DISPLAY_FLAG, ConsultReviewsDao::DISPLAY_FLAG_ON);
		$ConsultReviewsDao->addWhere(ConsultReviewsDao::COL_DELETE_FLAG, ConsultReviewsDao::DELETE_FLAG_ON);
		$ConsultReviewsDao->addOrder(ConsultReviewsDao::COL_CONSULT_REVIEW_ID, 'DESC');
		$ConsultReviewsDao->addLimit(200);
		$review_list = $ConsultReviewsDao->select();
		if (count($review_list)>0) {
			foreach ($review_list as $d) {
				$this->_setSitemapList($list, 'advice/review/'.$d['consult_review_id'].'/', $ndt, 'weekly', '0.5');
			}
		}

		foreach (AppConst::$mainCategorys as $cid => $cname) {
			$this->_setSitemapList($list, 'category/'.$cid.'/', $ndt, 'daily', '0.8');
		}

		$this->_setSitemapList($list, 'search/', $ndt, 'daily', '1.0');
		$this->_setSitemapList($list, 'member/search/', $ndt, 'daily', '1.0');
		$this->_setSitemapList($list, 'consult/search/', $ndt, 'daily', '1.0');

		$alzSearchTagsDao = new AlzSearchTagsDao($this->db);
		$tag = $alzSearchTagsDao->getActiveTag(400);
		if (count($tag)>0) {
			foreach ($tag as $d) {
				$this->_setSitemapList($list, 'search/t/'.$d['search_tag'], $ndt, 'weekly', '0.5');
			}
		}

		$alzSearchWordsDao = new AlzSearchWordsDao($this->db);
		$word = $alzSearchWordsDao->getActiveWord(500);
		if (count($word)>0) {
			foreach ($word as $d) {
				if ($d['search_opt'] == '') {
					$this->_setSitemapList($list, 'search/'.$d['search_word'], $ndt, 'weekly', '0.5');
				} else {
					$opt = unserialize($d['search_opt']);
					if ($opt['search_type'] == 2) {
//						$this->_setSitemapList($list, 'member/search/'.$d['search_word'], $ndt, 'weekly', '0.5');
					} else if ($opt['search_type'] == 3) {
						$this->_setSitemapList($list, 'consult/search/'.$d['search_word'], $ndt, 'weekly', '0.5');
					}
				}
			}
		}

//		$usersDao = new UsersDao($this->db);
//		$user = $usersDao->getNewListOfPublic(200);
//		if (count($user)>0) {
//			foreach ($user as $d) {
//				$this->_setSitemapList($list, 'profile/'.$d['user_id'].'/', $ndt, 'weekly', '0.5');
//			}
//		}

		$this->form->set('list', $list);

		$this->resp->setContentType(SpResponse::CTYPE_XML);

		return $this->forward('sitemap', APP_CONST_EMPTY_FRAME);
	}

	private function _setSitemapList(&$list, $loc, $lastmod=null, $changefreq='hourly', $priority='1.0')
	{
		if ($lastmod===null) $lastmod = date('c');

		$list[] = array(
			'loc' => constant('app_site_url') . $loc,
			'lastmod' => $lastmod,
			'changefreq' => $changefreq,
			'priority' => $priority
		);
	}
}
?>
