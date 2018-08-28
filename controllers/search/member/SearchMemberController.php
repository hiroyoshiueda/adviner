<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('AlzSearchWordsDao', 'dao');
/**
 * 人を検索(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class SearchMemberController extends BaseController
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
		$sort = $this->form->get('sort');

		$words = array();
		$search_id = 0;
		$alzSearchDao = null;
		$pagerf = '';
		$htitle = '';

		$userInfo = $this->getUserInfo();

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
				$search_id = $alzSearchDao->register($q, 2);
			}

			$pagerf = '/member/search/%d/'.$q;
			$htitle = $q.' を含むメンバー';
			$this->setDescription('「'.$q.'」を含むメンバー・専門家・アドバイザーです。'.$pagestr);
		}
		else
		{
			$pagerf = '/member/search/%d/';
			$htitle = 'すべてのメンバー';
			$this->setDescription('すべてのメンバー・専門家・アドバイザーです。'.$pagestr);
		}

		$UsersDao = new UsersDao($this->db);
		if ($sort == 'created') {
			$orderby = array('u.createdate'=>'DESC');
			$pagerf .= '?sort=' . $sort;
		} else {
			$orderby = array('ur.point'=>'DESC');
		}
		$list = $UsersDao->getPageListOnSearch($total, $offset, $limit, $orderby, $words);
		$this->form->set('list', $list);
		$this->form->set('list_total', $total);
		$this->form->set('list_limit', $limit);
		$this->form->set('list_offset', $offset);
		$this->form->set('list_pagenum', $pagenum);

		// 検索結果が存在する場合
		if ($alzSearchDao !== null && $total > 0 && $search_id > 0) {
			$alzSearchDao->exist($search_id);
		}

		$this->form->set('qtype', '2');
		$this->form->set('pagerf', $pagerf);
		$this->form->set('htitle', $htitle);
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('search/member/search_member_index', APP_CONST_MAIN_FRAME);
	}
}
?>
