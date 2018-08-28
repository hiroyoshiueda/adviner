<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('CategorysDao', 'dao');
Sp::import('AlzSearchTagsDao', 'dao');
Sp::import('AlzSearchWordsDao', 'dao');
/**
 * 相談窓口検索(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class SearchController extends BaseController
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
		$category_id = 0;
		$main_category_id = $this->form->get('main_category_id');
		if (strpos($main_category_id, '_') !== false) {
			$cate_arr = explode('_', $main_category_id);
			$main_category_id = (int)$cate_arr[0];
			$category_id = (int)$cate_arr[1];
		} else {
			$main_category_id = $this->form->getInt('main_category_id');
		}
		$sort = $this->form->get('sort');

		// 保護処置
		$qtype = $this->form->getInt('qtype', 1);
		if ($qtype == 2) {
			return $this->resp->sendRedirect('/member/search/'.$q);
		} else if ($qtype == 3) {
			return $this->resp->sendRedirect('/consult/search/'.$q);
		}

		$qopt = 'default';
		// 非公開検索オプション
		if ($q != '' && substr($q, 0, 8) == 'private:')
		{
			$qopt = 'private';
			$q = substr($q, 8);
		}

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

		if ($category_id > 0)
		{
			$category_ids = array($category_id);
			$cname = $category_set[$category_id]['cname'];
			$this->form->set('search_category', $main_category_id.'_'.$category_id);
		}
		else if ($main_category_id > 0)
		{
			$categorysDao = new CategorysDao($this->db);
			$category = $categorysDao->getListByMainCategoryId($main_category_id);
			$category_ids = Util::arraySelectKey('category_id', $category);
			$cname = AppConst::$mainCategorys[$main_category_id];
			$this->form->set('search_category', $main_category_id);
		}

		$pagestr = ($pagenum>1) ? $pagenum.'ページ目を表示します。' : '';
		$pageopt = ($qopt == 'private') ? 'private:' : '';

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
					if ($q_str == '就職活動') {
						$q_str = '就活';
					} else if ($q_str == 'blog') {
						$q_str = 'ブログ';
					}
					if ($q_str != '相談する' && $q_str != '相談' && $q_str != 'アドバイス') {
						$words[] = $q_str;
					}
					//if ($q_str == 'フェイスブック') $words[] = 'facebook';
					//elseif ($q_str == 'facebook') $words[] = 'フェイスブック';
					// 複数キーワードは3つまで
					if (count($words)>=3) break;
				}
			}
			if ($offset == 0) {
				$alzSearchDao = new AlzSearchWordsDao($this->db);
				$search_id = $alzSearchDao->register($q);
			}
			if ($main_category_id > 0) {
				$pagerf = '/category/'.$main_category_id.'/search/%d/'.$pageopt.$q;
				$htitle = $q.' と '.$cname.' に関する相談窓口';
				$this->setDescription($q.'と'.$cname.'に関する相談窓口です。注目の「'.$q.'」に関する専門家やアドバイザーに質問、相談することができます。'.$pagestr);
			} else {
				$pagerf = '/search/%d/'.$pageopt.$q;
				$htitle = $q.' に関する相談窓口';
				$this->setDescription($q.'に関する相談窓口です。注目の「'.$q.'」に関する専門家やアドバイザーに質問、相談することができます。'.$pagestr);
			}
		}
		else if ($t != '')
		{
			// タグ検索
			$t = str_replace(array("\n","\r","\t","\0","\x0B"), '', $t);
			$t = mb_substr($t, 0, 100);
			if ($offset == 0 && $t != '') {
				$alzSearchDao = new AlzSearchTagsDao($this->db);
				$search_id = $alzSearchDao->register($t);
			}
			if ($main_category_id > 0) {
				$pagerf = '/category/'.$main_category_id.'/search/t/%d/'.$t;
				$htitle = 'タグ「'.$t.'」を含む '.$cname.' に関する相談窓口';
				$this->setDescription('「'.$t.'」タグのついた'.$cname.'に関する相談窓口を探すことができます。注目の「'.$t.'」に関する専門家やアドバイザーに質問、相談することができます。'.$pagestr);
			} else {
				$pagerf = '/search/t/%d/'.$t;
				$htitle = 'タグ「'.$t.'」を含む相談窓口';
				$this->setDescription('「'.$t.'」タグのついた相談窓口を探すことができます。注目の「'.$t.'」に関する専門家やアドバイザーに質問、相談することができます。'.$pagestr);
			}
		}
		else
		{
			if ($category_id > 0) {
				$pagerf = '/category/'.$main_category_id.'_'.$category_id.'/%d/'.$pageopt;
				$htitle = $cname.' に関する相談窓口';
				$this->setDescription($cname.'に関する相談窓口です。'.$cname.'の専門家やアドバイザーを探して質問、相談することができます。'.$pagestr);
			} else if ($main_category_id > 0) {
				$pagerf = '/category/'.$main_category_id.'/%d/'.$pageopt;
				$htitle = $cname.' に関する相談窓口';
				$this->setDescription($cname.'に関する相談窓口です。'.$cname.'の専門家やアドバイザーを探して質問、相談することができます。'.$pagestr);
			} else {
				$pagerf = '/search/%d/'.$pageopt;
				$htitle = '受付中の相談窓口';
				$this->setDescription('受付中の相談窓口です。いろいろな専門家やアドバイザーを探して質問、相談することができます。'.$pagestr);
			}
		}

		$advicesDao = new AdvicesDao($this->db);
		if ($sort == 'created')
		{
			$list = $advicesDao->getNewPageList($total, $offset, $limit, $category_ids, $words, $t, $qopt);
			$pagerf .= '?sort=' . $sort;
			$htitle .= '（新着順）';
		}
		else
		{
			$list = $advicesDao->getPopularPageList($total, $offset, $limit, $category_ids, $words, $t, $qopt);
			$htitle .= '（人気順）';
		}
		$this->form->set('list', $list);
		$this->form->set('list_total', $total);
		$this->form->set('list_limit', $limit);
		$this->form->set('list_offset', $offset);
		$this->form->set('list_pagenum', $pagenum);

		// 検索結果が存在する場合
		if ($alzSearchDao !== null && $total > 0 && $search_id > 0) {
			$alzSearchDao->exist($search_id);
		}

//		if ($this->checkUserAuth() && $total > 0)
//		{
//			$advice_ids = Util::arraySelectKey('advice_id', $list, true);
//			$FollowsDao = new FollowsDao($this->db);
//			$follow_list = $FollowsDao->getAdviceList($userInfo['id'], $advice_ids);
//			$this->form->set('follow_set', Util::arrayKeyData('follow_advice_id', $follow_list));
//		}

		$this->setLoadUserRank(new UserRanksDao($this->db));

		$this->form->set('q', $q);
		$this->form->set('qopt', $qopt);
		$this->form->set('pagerf', $pagerf);
		$this->form->set('htitle', $htitle);
		$this->setTitle($this->form->get('htitle'), '', APP_CONST_SITE_TITLE4);

		return $this->forward('search/search_index', APP_CONST_MAIN_FRAME);
	}
}
?>
