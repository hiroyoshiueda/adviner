<?php
/**
 * 相談マスタ
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ConsultsDao extends BaseDao
{
	const TABLE_NAME = 'consults';

	const COL_CONSULT_ID = "consult_id";
	const COL_DELETE_FLAG = "delete_flag";
	const COL_DISPLAY_FLAG = "display_flag";
	const COL_CONSULT_STATUS = "consult_status";
	const COL_ADVICE_ID = "advice_id";
	const COL_ADVICE_USER_ID = "advice_user_id";
	const COL_CONSULT_USER_ID = "consult_user_id";
	const COL_PUBLIC_FLAG = "public_flag";
	const COL_CONSULT_BODY = "consult_body";
	const COL_DEMAND_BODY = "demand_body";
	const COL_PERIOD_TYPE = "period_type";
	const COL_LATEST_REPLY_ID = "latest_reply_id";
	const COL_LATEST_REPLY_BODY = "latest_reply_body";
	const COL_LATEST_REPLY_DATE = "latest_reply_date";
	const COL_REVIEW_STATE = "review_state";
	const COL_REVIEW_PUBLIC_FLAG = "review_public_flag";
	const COL_PLEASE_FLAG = "please_flag";
	const COL_ADVICE_CHARGE_FLAG = "advice_charge_flag";
	const COL_ADVICE_CHARGE_PRICE = "advice_charge_price";
	const COL_ORDER_ID = "order_id";
	const COL_ORDER_STATUS = "order_status";
	const COL_FINISHDATE = "finishdate";
	const COL_REPLYDATE = "replydate";
	const COL_CREATEDATE = "createdate";
	const COL_LASTUPDATE = "lastupdate";
	const COL_DELETEDATE = "deletedate";

	const CONSULT_STATUS_WAIT = 0;
	const CONSULT_STATUS_DURING = 1;
	const CONSULT_STATUS_FINISH = 2;
	const CONSULT_STATUS_CONSULT_FINISH = 3;

	const PUBLIC_FLAG_PRIVATE = 1;
	const PUBLIC_FLAG_PUBLIC = 2;

	const REVIEW_STATE_WAIT = 1;
	const REVIEW_STATE_WRITED = 2;
	const REVIEW_STATE_NOWRITE = 3;

	const REVIEW_PUBLIC_FLAG_PRIVATE = 1;
	const REVIEW_PUBLIC_FLAG_PUBLIC = 2;

	const PLEASE_FLAG_OFF = 0;
	const PLEASE_FLAG_ON = 1;

	const CHARGE_FLAG_FREE = 0;
	const CHARGE_FLAG_CHARGE = 1;

	const ORDER_STATUS_RECEIVE = 2;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList($limit=0)
	{
		$this->addWhere(self::COL_PUBLIC_FLAG, self::PUBLIC_FLAG_PUBLIC);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addOrder(self::COL_CONSULT_ID, 'DESC');
		$this->addLimit($limit);
		return $this->select();
	}

	public function getItem($id)
	{
		$this->addWhere(self::COL_CONSULT_ID, $id);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function getItemByAdviceId($consult_id, $advice_id)
	{
		$this->addWhere(self::COL_CONSULT_ID, $consult_id);
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function delete($id, $user_id=0)
	{
		$this->addWhere(self::COL_CONSULT_ID, $id);
		if ($user_id>0) $this->addWhere(self::COL_USER_ID, $user_id);
		return $this->doDelete();
	}

	public function getListByUserId($advice_id, $user_id)
	{
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		$this->addWhere(self::COL_CONSULT_USER_ID, $user_id);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
//		$this->addSelect('c.*');
//		$this->addSelect('u.'.UsersDao::COL_LOGIN);
//		$this->addSelect('u.'.UsersDao::COL_PENNAME);
//		$this->addSelectAs('u.'.UsersDao::COL_PROFILE_S_PATH, 'profile_path');
//		$this->setTable(self::TABLE_NAME, 'c');
//		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'c.post_user_id=u.user_id');
//		$this->addWhere('c.'.self::COL_USER_ID, $user_id);
//		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
//		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
//		$this->addWhere('u.'.UsersDao::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
//		$this->addOrder('c.'.self::COL_CREATEDATE, 'DESC');
		return $this->select();
	}

	/**
	 * 公開区分＆回答のあった相談のみ取得
	 */
	public function getPageListOfAdvice(&$total, $offset, $limit, $advice_id)
	{
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		$wh1 = '('.self::COL_PUBLIC_FLAG.'='.self::PUBLIC_FLAG_PUBLIC.' AND '.self::COL_LATEST_REPLY_ID.'>0)';
		$wh2 = '('.self::COL_PUBLIC_FLAG.'='.self::PUBLIC_FLAG_PRIVATE.' AND '.self::COL_REVIEW_PUBLIC_FLAG.'='.self::REVIEW_PUBLIC_FLAG_PUBLIC.')';
		$this->addWhere('', '('.$wh1.' OR '.$wh2.')');
		//$this->addWhere(self::COL_PUBLIC_FLAG, self::PUBLIC_FLAG_PUBLIC);
		//$this->addWhere(self::COL_LATEST_REPLY_ID, 0, '>');
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		//$this->addOrder(self::COL_CONSULT_ID, 'DESC');
		$this->addOrder(self::COL_REPLYDATE, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	public function getPageListOfAdviceAll(&$total, $offset, $limit, $advice_id, $user_id)
	{
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		$this->addWhere('', '('.self::COL_ADVICE_USER_ID.'='.$user_id.' OR '.self::COL_CONSULT_USER_ID.'='.$user_id.')');
		$this->addWhere(self::COL_PUBLIC_FLAG, self::PUBLIC_FLAG_PUBLIC);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addOrder(self::COL_CONSULT_ID, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	/**
	 * アドバイザーマイページの相談リスト
	 * @see /user/mypage/advice/
	 */
	public function getListOfAdvisorMypage(&$total, $offset, $limit, $advice_id, $user_id)
	{
		$this->addSelect('c.*');
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);

		$this->setTable(self::TABLE_NAME, 'c');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'c.consult_user_id=u.user_id');

		$this->addWhere('c.'.self::COL_ADVICE_ID, $advice_id);
		$this->addWhere('c.'.self::COL_ADVICE_USER_ID, $user_id);
		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addOrder('c.'.self::COL_CREATEDATE, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	public function getListOfMypage(&$total, $offset, $limit, $user_id)
	{
		$this->addSelect('c.*');
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TITLE);
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);

		$this->setTable(self::TABLE_NAME, 'c');
		$this->addTableJoin(AdvicesDao::TABLE_NAME, 'a', 'c.advice_id=a.advice_id', 'INNER');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'c.advice_user_id=u.user_id', 'INNER');

		$this->addWhere('c.'.self::COL_CONSULT_USER_ID, $user_id);
		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addOrder('c.'.self::COL_CREATEDATE, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	public function getAdviceHistoryOfMypage(&$total, $offset, $limit, $user_id)
	{
		$this->addSelect('c.*');
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TITLE);
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);

		$this->setTable(self::TABLE_NAME, 'c');
		$this->addTableJoin(AdvicesDao::TABLE_NAME, 'a', 'c.advice_id=a.advice_id', 'INNER');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'c.consult_user_id=u.user_id', 'INNER');

		$this->addWhere('c.'.self::COL_ADVICE_USER_ID, $user_id);
		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addOrder('c.'.self::COL_CREATEDATE, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	public function getListOfPublic($user_id)
	{
		$this->addSelect('c.*');
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TITLE);
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);

		$this->setTable(self::TABLE_NAME, 'c');
		$this->addTableJoin(AdvicesDao::TABLE_NAME, 'a', 'c.advice_id=a.advice_id');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'c.advice_user_id=u.user_id');

		$this->addWhere('c.'.self::COL_CONSULT_USER_ID, $user_id);
		$this->addWhere('c.'.self::COL_LATEST_REPLY_ID, 0, '>');
		$this->addWhere('c.'.self::COL_PUBLIC_FLAG, self::PUBLIC_FLAG_PUBLIC);
		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addOrder('c.'.self::COL_CREATEDATE, 'DESC');
		$this->addLimit(10);

		return $this->select();
	}

	public function getNewPageList($limit, $offset, &$total)
	{
		$this->addSelect('c.'.self::COL_CONSULT_ID);
		$this->addSelect('c.'.self::COL_ADVICE_ID);
		$this->addSelect('c.'.self::COL_ADVICE_USER_ID);
		$this->addSelect('c.'.self::COL_CONSULT_USER_ID);
		$this->addSelect('c.'.self::COL_PUBLIC_FLAG);
		$this->addSelect('c.'.self::COL_CONSULT_BODY);
		$this->addSelect('c.'.self::COL_CREATEDATE);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TITLE);

		$this->setTable(self::TABLE_NAME, 'c');
		$this->addTableJoin(AdvicesDao::TABLE_NAME, 'a', 'c.advice_id=a.advice_id');

		$this->addWhere('c.'.self::COL_LATEST_REPLY_ID, 0, '>');
		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addOrder('c.'.self::COL_REPLYDATE, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	/**
	 * Feed用フォロー対象リスト取得
	 * @param unknown_type $total
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $followData
	 */
	public function getFeedPageListByFollowData(&$total, $offset, $limit, &$followData)
	{
		$this->addSelect('c.'.self::COL_CONSULT_ID);
		$this->addSelect('c.'.self::COL_ADVICE_ID);
		$this->addSelect('c.'.self::COL_ADVICE_USER_ID);
		$this->addSelect('c.'.self::COL_CONSULT_USER_ID);
		$this->addSelect('c.'.self::COL_PUBLIC_FLAG);
		$this->addSelect('c.'.self::COL_CONSULT_BODY);
		$this->addSelect('c.'.self::COL_CREATEDATE);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TITLE);

		$this->setTable(self::TABLE_NAME, 'c');
		$this->addTableJoin(AdvicesDao::TABLE_NAME, 'a', 'c.advice_id=a.advice_id');

		$wh1 = '';
		if (empty($followData['user_id']) === false)
		{
			$wh1 = 'c.'.self::COL_CONSULT_USER_ID.' IN ('.implode(',', $followData['user_id']).')';
		}

		$wh2 = '';
		if (empty($followData['advice_id']) === false)
		{
			$wh2 = 'c.'.self::COL_ADVICE_ID.' IN ('.implode(',', $followData['advice_id']).')';
		}

		if ($wh1 != '' && $wh2 != '') {
			$this->addWhere('', '('.$wh1.' OR '.$wh2.')');
		} else if ($wh1 != '') {
			$this->addWhere('', $wh1);
		} else if ($wh2 != '') {
			$this->addWhere('', $wh2);
		}

		$this->addWhere('c.'.self::COL_LATEST_REPLY_ID, 0, '>');
		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addOrder('c.'.self::COL_REPLYDATE, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	public function getConsultList($consult_ids)
	{
		$this->addSelect(self::COL_CONSULT_ID);
		$this->addSelect(self::COL_CONSULT_STATUS);
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addSelect(self::COL_CONSULT_USER_ID);
		$this->addSelect(self::COL_PUBLIC_FLAG);
		$this->addSelect(self::COL_CONSULT_BODY);
		$this->addSelect(self::COL_LATEST_REPLY_ID);
		$this->addSelect(self::COL_CREATEDATE);

		$consult_ids = array_unique($consult_ids);
		$cnt = count($consult_ids);
		if ($cnt == 0) {
			return array();
		} else if ($cnt == 1) {
			$this->addWhere(self::COL_CONSULT_ID, $consult_ids[0]);
		} else {
			$this->addWhereIn(self::COL_CONSULT_ID, $consult_ids);
		}
		//$this->addWhere(self::COL_LATEST_REPLY_ID, 0, '>');
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->select();
	}

	/**
	 * Feed用(アドバイスくださいのみ)
	 * @see FeedStream.php
	 */
	public function getFeedListByFollowData(&$followData, $latestDate, $limit)
	{
		$this->addSelect(self::COL_CONSULT_ID);
		$this->addSelect(self::COL_CONSULT_USER_ID);
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
				$wh1 = self::COL_CONSULT_USER_ID.' IN ('.implode(',', $followData['user_id']).')';
			}

			$wh2 = '';
			if (empty($followData['advice_id']) === false)
			{
				$wh2 = self::COL_ADVICE_ID.' IN ('.implode(',', $followData['advice_id']).')';
			}

			if ($wh1 != '' && $wh2 != '') {
				$this->addWhere('', '('.$wh1.' OR '.$wh2.')');
			} else if ($wh1 != '') {
				$this->addWhere('', '('.$wh1.')');
			} else if ($wh2 != '') {
				$this->addWhere('', $wh2);
			}
		}

		$this->addWhere(self::COL_CONSULT_STATUS, self::CONSULT_STATUS_WAIT);
		$this->addWhere(self::COL_PLEASE_FLAG, self::PLEASE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addLimit($limit);

		return $this->select();
	}

	/**
	 * Action用
	 * @param unknown_type $total
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $user_id
	 */
	public function getMyActionPageList(&$total, $offset, $limit, $user_id)
	{
		$this->addSelect(self::COL_CONSULT_ID);
		$this->addSelect(self::COL_CONSULT_STATUS);
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addSelect(self::COL_CONSULT_USER_ID);
		$this->addSelect(self::COL_PUBLIC_FLAG);
		$this->addSelect(self::COL_CONSULT_BODY);
		$this->addSelect(self::COL_LATEST_REPLY_ID);
		$this->addSelect(self::COL_REVIEW_STATE);
		$this->addSelect(self::COL_ADVICE_CHARGE_FLAG);
		$this->addSelect(self::COL_ADVICE_CHARGE_PRICE);
		$this->addSelect(self::COL_ORDER_STATUS);
		$this->addSelect(self::COL_CREATEDATE);

		$wh = sprintf("(%s=%d OR %s=%d)", self::COL_ADVICE_USER_ID, $user_id, self::COL_CONSULT_USER_ID, $user_id);
		$this->addWhere('', $wh);
		$this->addWhere(self::COL_CONSULT_STATUS, self::CONSULT_STATUS_DURING);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addOrder(self::COL_REPLYDATE, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	/**
	 * アドバイスくださいリスト
	 */
	public function getPleaseAdvicePageList(&$total, $offset, $limit)
	{
		$this->addSelect('c.'.self::COL_CONSULT_ID);
		$this->addSelect('c.'.self::COL_CONSULT_STATUS);
		$this->addSelect('c.'.self::COL_CONSULT_USER_ID);
		$this->addSelect('c.'.self::COL_PUBLIC_FLAG);
		$this->addSelect('c.'.self::COL_CONSULT_BODY);
		$this->addSelect('c.'.self::COL_LATEST_REPLY_ID);
		$this->addSelect('c.'.self::COL_REVIEW_STATE);
		$this->addSelect('c.'.self::COL_CREATEDATE);
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);

		$this->setTable(self::TABLE_NAME, 'c');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'c.consult_user_id=u.user_id');

		$this->addWhere('c.'.self::COL_CONSULT_STATUS, self::CONSULT_STATUS_WAIT);
		$this->addWhere('c.'.self::COL_PLEASE_FLAG, self::PLEASE_FLAG_ON);
		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addOrder('c.'.self::COL_CREATEDATE, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}

	public function getItemAndUser($consult_id)
	{
		$this->addSelect('c.*');
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_OPEN_URL);
		$this->addSelect('u.'.UsersDao::COL_URL);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_MSG);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);

		$this->setTable(self::TABLE_NAME, 'c');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'c.consult_user_id=u.user_id');

		$this->addWhere('c.'.self::COL_CONSULT_ID, $consult_id);
		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		return $this->selectRow();
	}

	/**
	 * @param int $total
	 * @param int $offset
	 * @param int $limit
	 * @see ApiTopConsultController.php
	 */
	public function getPageListAndAdviceOfPublic(&$total, $offset, $limit)
	{
		$this->addSelect('c.*');
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TITLE);

		$this->setTable(self::TABLE_NAME, 'c');
		$this->addTableJoin(AdvicesDao::TABLE_NAME, 'a', 'c.advice_id=a.advice_id');

		//$wh1 = '(c.'.self::COL_PUBLIC_FLAG.'='.self::PUBLIC_FLAG_PUBLIC.' AND c.'.self::COL_LATEST_REPLY_ID.'>0)';
		//$wh2 = '(c.'.self::COL_PUBLIC_FLAG.'='.self::PUBLIC_FLAG_PRIVATE.' AND c.'.self::COL_REVIEW_PUBLIC_FLAG.'='.self::REVIEW_PUBLIC_FLAG_PUBLIC.')';
		//$this->addWhere('', '('.$wh1.' OR '.$wh2.')');
		$this->addWhere('c.'.self::COL_PUBLIC_FLAG, self::PUBLIC_FLAG_PUBLIC);
		$this->addWhere('c.'.self::COL_LATEST_REPLY_ID, 0, '>');
		$this->addWhere('c.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere('a.'.AdvicesDao::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addOrder('c.'.self::COL_REPLYDATE, 'DESC');

		return $this->selectPage($offset, $limit, $total);
	}
}
?>