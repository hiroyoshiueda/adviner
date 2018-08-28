<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('CategorysDao', 'dao');
Sp::import('AlzSearchWordsDao', 'dao');
/**
 * 相談内容の検索(Controller)
 * search_type: 3
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class SearchConsultController extends BaseController
{
	/**
	 * トップ表示
	 */
	public function index()
	{
		$total = 0;
		$limit = 10;
		$offset = $this->form->getInt('offset');
		// pagenumは1～
		$pagenum = $this->form->getInt('pagenum');
		if ($pagenum>0) {
			$offset = ($pagenum - 1) * $limit;
			$this->form->set('offset', $offset);
		}
		$q = $this->form->get('q');
		$t = $this->form->get('t');
//		$category_id = $this->form->getInt('category_id');
		$main_category_id = $this->form->getInt('main_category_id');
		$sort = $this->form->get('sort');

		$words = array();
		$search_id = 0;
		$alzSearchDao = null;
		$category_ids = null;
		$cname = '';
		$pagerf = '';
		$htitle = '';

		$userInfo = $this->getUserInfo();

		// カテゴリ検索準備
		$category_set = $this->form->get('category_set');

		if ($main_category_id > 0)
		{
			$categorysDao = new CategorysDao($this->db);
			$category = $categorysDao->getListByMainCategoryId($main_category_id);
			$category_ids = Util::arraySelectKey('category_id', $category);
			$cname = AppConst::$mainCategorys[$main_category_id];
			$this->form->set('search_category', $main_category_id);
		}
//		else if ($category_id > 0)
//		{
//			$category_ids = array($category_id);
//			$cname = $category_set[$category_id]['cname'];
//		}

		$pagestr = ($pagenum>1) ? $pagenum.'ページ目を表示します。' : '';

		// 検索準備
		if ($q != '')
		{
			// キーワード検索
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

			if ($offset == 0) {
				$alzSearchDao = new AlzSearchWordsDao($this->db);
				$search_id = $alzSearchDao->register($q, 3);
			}

			if ($main_category_id > 0) {
				$pagerf = '/category/'.$main_category_id.'/consult/search/%d/'.$q;
				$htitle = $q.' と '.$cname.' に関する相談・アドバイス';
				$this->setDescription($q.'と'.$cname.'に関する相談・アドバイスです。注目の「'.$q.'」に関する専門家やアドバイザーに気軽に相談することができます。'.$pagestr);
			} else {
				$pagerf = '/consult/search/%d/'.$q;
				$htitle = $q.' に関する相談・アドバイス';
				$this->setDescription($q.'に関する相談・アドバイスです。注目の「'.$q.'」に関する専門家やアドバイザーに気軽に相談することができます。'.$pagestr);
			}
		}
		else
		{
			$pagerf = '/consult/search/%d/';
			$htitle = '相談・アドバイス';
			$this->setDescription('相談・アドバイスです。様々な専門家やアドバイザーを探して相談することができます。'.$pagestr);
		}

		$ConsultReplysDao = new ConsultReplysDao($this->db);
		if ($sort == 'created') {
			$orderby = array('cr.createdate'=>'DESC');
			$pagerf .= '?sort=' . $sort;
		} else {
			$orderby = array('cr.createdate'=>'DESC');
		}
		$list = $ConsultReplysDao->getPageListOnSearch($total, $offset, $limit, $orderby, $words);
		$this->form->set('list', $list);
		$this->form->set('list_total', $total);
		$this->form->set('list_limit', $limit);
		$this->form->set('list_offset', $offset);
		$this->form->set('list_pagenum', $pagenum);

		// 検索結果が存在する場合
		if ($alzSearchDao !== null && $total > 0 && $search_id > 0) {
			$alzSearchDao->exist($search_id);
		}

		if (count($list) > 0)
		{
			$user_ids = Util::arraySelectKey('advice_user_id', $list);
			$consult_user_ids = Util::arraySelectKey('consult_user_id', $list);
			$user_ids = array_merge($user_ids, $consult_user_ids);
			$UsersDao = new UsersDao($this->db);
			$user_list = $UsersDao->getListByIds($user_ids);
			$this->form->set('user_set', Util::arrayKeyData('user_id', $user_list));
		}

//		if ($this->checkUserAuth() && $total > 0)
//		{
//			$advice_ids = Util::arraySelectKey('advice_id', $list, true);
//			$FollowsDao = new FollowsDao($this->db);
//			$follow_list = $FollowsDao->getAdviceList($userInfo['id'], $advice_ids);
//			$this->form->set('follow_set', Util::arrayKeyData('follow_advice_id', $follow_list));
//		}

		$this->setLoadUserRank(new UserRanksDao($this->db));

		$this->form->set('qtype', '3');
		$this->form->set('pagerf', $pagerf);
		$this->form->set('htitle', $htitle);
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('search/consult/search_consult_index', APP_CONST_MAIN_FRAME);
	}
}
?>
