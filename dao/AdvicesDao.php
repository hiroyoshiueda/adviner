<?php
/**
 * 相談窓口
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class AdvicesDao extends BaseDao
{
	const TABLE_NAME = 'advices';

	const COL_ADVICE_ID = "advice_id";
	const COL_DELETE_FLAG = "delete_flag";
	const COL_DISPLAY_FLAG = "display_flag";
	const COL_ADVICE_USER_ID = "advice_user_id";
	const COL_CATEGORY_ID = "category_id";
	const COL_ADVICE_STATUS = "advice_status";
	const COL_ADVICE_TITLE = "advice_title";
	const COL_ADVICE_BODY = "advice_body";
	const COL_ADVICE_TAG = "advice_tag";
	const COL_ADVICE_TAG_SEARCH = "advice_tag_search";
	const COL_PUBLIC_TYPE = "public_type";
	const COL_FREQUENCY_TYPE = "frequency_type";
	const COL_CONDITIONS = "conditions";
	const COL_ADVICE_FB_SHARE = "advice_fb_share";
	const COL_PV_TOTAL = "pv_total";
	const COL_PV_TODAY = "pv_today";
	const COL_CONSULT_TOTAL = "consult_total";
	const COL_CONSULT_TODAY = "consult_today";
	const COL_WATCH_TOTAL = "watch_total";
	const COL_ADVICE_POINT = "advice_point";
	const COL_SEARCH_DATA = "search_data";
	const COL_CHARGE_FLAG = "charge_flag";
	const COL_CHARGE_PRICE = "charge_price";
	const COL_CHARGE_COUNT = "charge_count";
	const COL_CHARGE_BODY = "charge_body";
	const COL_REPLYDATE = "replydate";
	const COL_CREATEDATE = "createdate";
	const COL_LASTUPDATE = "lastupdate";
	const COL_DELETEDATE = "deletedate";

	const ADVICE_STATUS_STOP = 0;
	const ADVICE_STATUS_OK = 1;
	const ADVICE_STATUS_EXAMINE = 2;
	const ADVICE_STATUS_REFUSE = 3;

	const PUBLIC_TYPE_PUBLIC = 0;
	const PUBLIC_TYPE_PRIVATE = 1;

	const CHARGE_FLAG_FREE = 0;
	const CHARGE_FLAG_CHARGE = 1;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList()
	{
		return $this->select();
	}

	public function getItem($id)
	{
		$this->addWhere(self::COL_ADVICE_ID, $id);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function delete($id, $user_id)
	{
		$this->reset();
		$this->addValue(self::COL_DELETE_FLAG, self::DELETE_FLAG_OFF);
		$this->addValue(self::COL_DELETEDATE, Dao::DATE_NOW);
		$this->addWhere(self::COL_ADVICE_ID, $id);
		$this->addWhere(self::COL_ADVICE_USER_ID, $user_id);
		return $this->doUpdate();
	}

	public function getItemByUserId($id, $user_id)
	{
		$this->addWhere(self::COL_ADVICE_ID, $id);
		$this->addWhere(self::COL_ADVICE_USER_ID, $user_id);
		return $this->selectRow();
	}

	public function getItemOfPublic($id)
	{
		$this->addWhere(self::COL_ADVICE_ID, $id);
		$this->addWhere(self::COL_ADVICE_STATUS, self::ADVICE_STATUS_OK);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function getListOfMypage($user_id)
	{
		$this->addWhere(self::COL_ADVICE_USER_ID, $user_id);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addOrder(self::COL_ADVICE_ID, 'DESC');
		return $this->select();
	}

	public function getListOfPublic($user_id)
	{
		$this->addWhere(self::COL_ADVICE_USER_ID, $user_id);
		$this->addWhere(self::COL_ADVICE_STATUS, self::ADVICE_STATUS_OK);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
		return $this->select();
	}

	public function getPopularPageList(&$total, $offset, $limit, $category_ids=null, $words=null, $tag=null, $opt=null)
	{
		$this->_setHotList($category_ids, $words, $tag, $opt);

		$this->addOrder('a.'.self::COL_ADVICE_POINT, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	public function getPopularList($limit, $category_ids=null)
	{
		$this->_setHotList($category_ids, null, null, null);

		$this->addOrder('a.'.self::COL_ADVICE_POINT, 'DESC');
		$this->addLimit($limit);

		return $this->select();
	}

	public function getPopularNofollowList($user_id, $limit, $category_ids=null)
	{
		$this->setBaseList();
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);
		$this->addSelect('f.'.FollowsDao::COL_FOLLOW_ID);

		$this->setTable(self::TABLE_NAME, 'a');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'a.advice_user_id=u.user_id');
		$this->addTableJoin(FollowsDao::TABLE_NAME, 'f', 'a.advice_id=f.follow_advice_id AND f.user_id='.$user_id);

		$this->addWhereStr('f.'.FollowsDao::COL_FOLLOW_ID, null);

		if (is_array($category_ids)) {
			if (count($category_ids) == 1) {
				$this->addWhere('a.'.self::COL_CATEGORY_ID, $category_ids[0]);
			} else {
				$this->addWhereIn('a.'.self::COL_CATEGORY_ID, $category_ids);
			}
		}

		$this->addWhere('a.'.self::COL_ADVICE_STATUS, self::ADVICE_STATUS_OK);
		$this->addWhere('a.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('a.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_STATUS, UsersDao::STATUS_REGULAR);
		$this->addWhere('u.'.UsersDao::COL_DISPLAY_FLAG, UsersDao::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);

		$this->addOrder('a.'.self::COL_ADVICE_POINT, 'DESC');
		$this->addLimit($limit);

		return $this->select();
	}

	public function getRecentNofollowList($user_id, $limit)
	{
		$this->setBaseList();
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);
		$this->addSelect('f.'.FollowsDao::COL_FOLLOW_ID);

		$this->setTable(self::TABLE_NAME, 'a');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'a.advice_user_id=u.user_id');
		$this->addTableJoin(FollowsDao::TABLE_NAME, 'f', 'a.advice_id=f.follow_advice_id AND f.user_id='.$user_id);

		$this->addWhereStr('f.'.FollowsDao::COL_FOLLOW_ID, null);

		$this->addWhere('a.'.self::COL_ADVICE_STATUS, self::ADVICE_STATUS_OK);
		$this->addWhere('a.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('a.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_STATUS, UsersDao::STATUS_REGULAR);
		$this->addWhere('u.'.UsersDao::COL_DISPLAY_FLAG, UsersDao::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);

		$this->addOrder('a.'.self::COL_ADVICE_ID, 'DESC');
		$this->addLimit($limit);

		return $this->select();
	}

	public function getNewPageList(&$total, $offset, $limit, $category_ids=null, $words=null, $tag=null, $opt=null)
	{
		$this->_setHotList($category_ids, $words, $tag, $opt);

		$this->addOrder('a.'.self::COL_ADVICE_ID, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	public function getNewList($limit)
	{
		$this->_setHotList(null, null, null, null);

		$this->addOrder('a.'.self::COL_ADVICE_ID, 'DESC');
		$this->addLimit($limit);

		return $this->select();
	}

	public function getEndList($limit, $length, $not_id_list)
	{
		$this->_setHotList(null, null, null, null);
		$this->addWhere('CHAR_LENGTH('.'a.'.self::COL_ADVICE_BODY.')', $length, '>');
		$this->addWhereNotIn('a.'.self::COL_ADVICE_ID, $not_id_list);

		$this->addOrder('a.'.self::COL_ADVICE_POINT, 'ASC');
		$this->addOrder('a.'.self::COL_ADVICE_ID, 'DESC');
		$this->addLimit($limit);

		return $this->select();
	}

	public function getNewPageListByAdvice(&$total, $offset, $limit, $advice_ids)
	{
		$this->addWhereIn('a.'.self::COL_ADVICE_ID, $advice_ids);

		$this->_setHotList(null, null, null, null);

		$this->addOrder('a.'.self::COL_ADVICE_ID, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	public function getRecommendSet($advice_ids, $limit, $category_ids=null, $words=null, $tag=null, $opt=null)
	{
		$this->addWhereIn('a.'.self::COL_ADVICE_ID, $advice_ids);

		$this->_setHotList($category_ids, $words, $tag, $opt);
		//$this->addOrder('a.'.self::COL_ADVICE_POINT, 'DESC');
		$this->addLimit($limit);

		return $this->selectKeySet(self::COL_ADVICE_ID);
	}

	private function _setHotList($category_ids, $words, $tag, $opt)
	{
		$this->setBaseList();
		$this->addSelect('u.*');

		$this->setTable(self::TABLE_NAME, 'a');
		$this->addTableJoin(UserRanksDao::TABLE_NAME, 'ur', 'a.advice_user_id=ur.user_id');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'a.advice_user_id=u.user_id');

		if ($opt == 'private') {
			$this->addWhere('a.'.self::COL_PUBLIC_TYPE, self::PUBLIC_TYPE_PRIVATE);
		}

		if (is_array($category_ids)) {
			if (count($category_ids) == 1) {
				$this->addWhere('a.'.self::COL_CATEGORY_ID, $category_ids[0]);
			} else {
				$this->addWhereIn('a.'.self::COL_CATEGORY_ID, $category_ids);
			}
		}

		if (is_array($words)) {
			foreach ($words as $word) {
				$wh = '(a.'.self::COL_ADVICE_TITLE.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ' OR a.'.self::COL_ADVICE_BODY.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ' OR a.'.self::COL_ADVICE_TAG.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ' OR a.'.self::COL_SEARCH_DATA.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ' OR u.'.UsersDao::COL_NICKNAME.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ' OR u.'.UsersDao::COL_SEARCHNAME.' LIKE '.$this->quoteString('%'.$word.'%');
				//$wh .= ' OR u.'.UsersDao::COL_PROFILE_MSG.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ')';
				$this->addWhere('', $wh);
			}
		}

		if ($tag != '') {
			$this->addWhereLike('a.'.self::COL_ADVICE_TAG_SEARCH, '%['.$tag.']%');
		}

		$this->addWhere('a.'.self::COL_ADVICE_STATUS, self::ADVICE_STATUS_OK);
		$this->addWhere('a.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('a.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_STATUS, UsersDao::STATUS_REGULAR);
		$this->addWhere('u.'.UsersDao::COL_DISPLAY_FLAG, UsersDao::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);
	}

	public function setBaseList()
	{
		$this->addSelect('a.'.self::COL_ADVICE_ID);
		$this->addSelect('a.'.self::COL_CATEGORY_ID);
		$this->addSelect('a.'.self::COL_ADVICE_STATUS);
		$this->addSelect('a.'.self::COL_ADVICE_TITLE);
		$this->addSelect('a.'.self::COL_ADVICE_BODY);
		$this->addSelect('a.'.self::COL_ADVICE_TAG);
		$this->addSelect('a.'.self::COL_ADVICE_TAG_SEARCH);
		$this->addSelect('a.'.self::COL_PUBLIC_TYPE);
		$this->addSelect('a.'.self::COL_CONSULT_TOTAL);
		$this->addSelect('a.'.self::COL_PV_TOTAL);
		$this->addSelect('a.'.self::COL_PV_TODAY);
		$this->addSelect('a.'.self::COL_CHARGE_FLAG);
		$this->addSelect('a.'.self::COL_CHARGE_PRICE);
	}

	public function updateCountPv($advice_id)
	{
		$this->addValue(self::COL_PV_TOTAL, self::COL_PV_TOTAL.'+1');
		$this->addValue(self::COL_PV_TODAY, self::COL_PV_TODAY.'+1');
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		return $this->doUpdate();
	}

	public function updateCountConsult($advice_id)
	{
		$this->addValue(self::COL_CONSULT_TOTAL, self::COL_CONSULT_TOTAL.'+1');
		$this->addValue(self::COL_CONSULT_TODAY, self::COL_CONSULT_TODAY.'+1');
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		return $this->doUpdate();
	}

	public function getPageList(&$total, $offset, $limit)
	{
		$this->addOrder(self::COL_ADVICE_ID, 'DESC');
		return $this->selectPage($offset, $limit, $total);
	}

	public function getExamineList(&$total, $offset, $limit)
	{
		$this->addWhere(self::COL_ADVICE_STATUS, self::ADVICE_STATUS_EXAMINE);
		$this->addOrder(self::COL_ADVICE_ID, 'DESC');
		return $this->selectPage($offset, $limit, $total);
	}

	public function getIdListByCategoryId($category_id)
	{
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addWhere(self::COL_CATEGORY_ID, $category_id);
		return $this->select();
	}

	public function getAdviceList($advice_ids, $display_flag=0, $delete_flag=0)
	{
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_CATEGORY_ID);
		$this->addSelect(self::COL_ADVICE_STATUS);
		$this->addSelect(self::COL_ADVICE_TITLE);
		$this->addSelect(self::COL_ADVICE_BODY);
		$this->addSelect(self::COL_ADVICE_TAG);
		$this->addSelect(self::COL_PUBLIC_TYPE);
		$this->addSelect(self::COL_CONSULT_TOTAL);
		$this->addSelect(self::COL_PV_TOTAL);
		$this->addSelect(self::COL_PV_TODAY);
		$this->addSelect(self::COL_CHARGE_FLAG);
		$this->addSelect(self::COL_CHARGE_PRICE);

		$advice_ids = array_unique($advice_ids);
		$cnt = count($advice_ids);
		if ($cnt > 1) {
			$this->addWhereIn(self::COL_ADVICE_ID, $advice_ids);
		} else if ($cnt == 1) {
			$this->addWhere(self::COL_ADVICE_ID, $advice_ids[0]);
		} else {
			return array();
		}
		//$this->addWhere(self::COL_ADVICE_STATUS, self::ADVICE_STATUS_OK);
		if ($display_flag >= 0) $this->addWhere(self::COL_DISPLAY_FLAG, $display_flag);
		if ($delete_flag >= 0) $this->addWhere(self::COL_DELETE_FLAG, $delete_flag);
		return $this->select();
	}

	/**
	 * Feed用
	 * @see FeedStream.php
	 */
	public function getFeedListByFollowData(&$followData, $latestDate, $limit, $is_all=false)
	{
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addSelect(self::COL_CREATEDATE);

		if (empty($latestDate) === false)
		{
			$this->addWhereStr(self::COL_CREATEDATE, $latestDate, '>');
		}

		if ($is_all === false)
		{
			if (empty($followData['user_id']) && empty($followData['advice_id'])) return array();

			$wh1 = '';
			if (empty($followData['user_id']) === false)
			{
				$wh1 = self::COL_ADVICE_USER_ID.' IN ('.implode(',', $followData['user_id']).')';
			}

			$wh2 = '';
			if (empty($followData['advice_id']) === false)
			{
				$wh2 = self::COL_ADVICE_ID.' IN ('.implode(',', $followData['advice_id']).')';
			}

			if ($wh1 != '' && $wh2 != '') {
				$this->addWhere('', '('.$wh1.' OR '.$wh2.')');
			} else if ($wh1 != '') {
				$this->addWhere('', $wh1);
			} else if ($wh2 != '') {
				$this->addWhere('', $wh2);
			}
		}

		$this->addWhere(self::COL_ADVICE_STATUS, self::ADVICE_STATUS_OK);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addLimit($limit);

		return $this->select();
	}

	public function getItemAndUser($advice_id, $advice_user_id)
	{
		$this->addSelect('a.*');
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);

		$this->setTable(self::TABLE_NAME, 'a');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'a.advice_user_id=u.user_id');

		$this->addWhere('a.'.self::COL_ADVICE_ID, $advice_id);
		$this->addWhere('a.'.self::COL_ADVICE_USER_ID, $advice_user_id);
		$this->addWhere('a.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('a.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_STATUS, UsersDao::STATUS_REGULAR);
		$this->addWhere('u.'.UsersDao::COL_DISPLAY_FLAG, UsersDao::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);

		return $this->selectRow();
	}

	public function isChargeAdvice($user_id)
	{
		$this->addSelectCount(self::COL_ADVICE_ID, 'total');
		$this->addWhere(self::COL_ADVICE_USER_ID, $user_id);
		$this->addWhere(self::COL_CHARGE_FLAG, self::CHARGE_FLAG_CHARGE);
		$this->addWhereIn(self::COL_ADVICE_STATUS, array(self::ADVICE_STATUS_OK, self::ADVICE_STATUS_STOP));
		// 削除したものも対象
		//$this->addWhere(self::COL_DELETE_FLAG, parent::DELETE_FLAG_ON);
		//$this->addWhere(self::COL_DISPLAY_FLAG, parent::DISPLAY_FLAG_ON);
		return ($this->selectId()>0);
	}
}
?>