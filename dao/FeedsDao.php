<?php
/**
 * フィード管理
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class FeedsDao extends BaseDao
{
	const TABLE_NAME = 'feeds';

	const COL_USER_ID = "user_id";
	const COL_ADVICE_ID = "advice_id";
	const COL_ADVICE_USER_ID = "advice_user_id";
	const COL_CONSULT_ID = "consult_id";
	const COL_CONSULT_USER_ID = "consult_user_id";
	const COL_CONSULT_REPLY_ID = "consult_reply_id";
	const COL_CONSULT_REPLY_USER_ID = "consult_reply_user_id";
	const COL_CONSULT_REVIEW_ID = "consult_review_id";
	const COL_CONSULT_REVIEW_USER_ID = "consult_review_user_id";
	const COL_CREATEDATE = "createdate";

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList($user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		return $this->select();
	}

//	public function getItem($id)
//	{
//		$this->addWhere(self::COL_FOLLOW_ID, $id);
//		return $this->selectRow();
//	}
//
	public function delete($user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		return $this->doDelete();
	}

	public function getLatestDate($user_id)
	{
		$this->addSelectMax(self::COL_CREATEDATE, 'latestdate');
		$this->addWhere(self::COL_USER_ID, $user_id);
		$latestdate = $this->selectOne();
		return $latestdate;
	}

	public function saveStream($user_id, &$arrData, $createdate)
	{
		$this->reset();
		$this->addValue(self::COL_USER_ID, $user_id);
		if (isset($arrData['advice_id'])) {
			if (empty($arrData['advice_id']) || empty($arrData['advice_user_id'])) {
				throw new SpException('Not set advice_id or advice_user_id.');
			}
			$this->addValue(self::COL_ADVICE_ID, $arrData['advice_id']);
			$this->addValue(self::COL_ADVICE_USER_ID, $arrData['advice_user_id']);
		}
		if (isset($arrData['consult_id'])) {
			if (empty($arrData['consult_id']) || empty($arrData['consult_user_id'])) {
				throw new SpException('Not set consult_id or consult_user_id.');
			}
			$this->addValue(self::COL_CONSULT_ID, $arrData['consult_id']);
			$this->addValue(self::COL_CONSULT_USER_ID, $arrData['consult_user_id']);
		}
		if (isset($arrData['consult_reply_id'])) {
			if (empty($arrData['consult_reply_id']) || empty($arrData['consult_reply_user_id'])) {
				throw new SpException('Not set consult_reply_id or consult_reply_user_id.');
			}
			$this->addValue(self::COL_CONSULT_REPLY_ID, $arrData['consult_reply_id']);
			$this->addValue(self::COL_CONSULT_REPLY_USER_ID, $arrData['consult_reply_user_id']);
		}
		if (isset($arrData['consult_review_id'])) {
			if (empty($arrData['consult_review_id']) || empty($arrData['consult_review_user_id'])) {
				throw new SpException('Not set consult_review_id or consult_review_user_id.');
			}
			$this->addValue(self::COL_CONSULT_REVIEW_ID, $arrData['consult_review_id']);
			$this->addValue(self::COL_CONSULT_REVIEW_USER_ID, $arrData['consult_review_user_id']);
		}
		$this->addValueStr(self::COL_CREATEDATE, $createdate);
		$this->doInsert();
		return $this->getLastInsertId();
	}

//	public function saveStreamForUpdate($user_id, &$arrData, $createdate)
//	{
//		$this->reset();
//		$this->addSelect(self::COL_FEED_ID);
//		$this->addWhere(self::COL_USER_ID, $user_id);
//		if (isset($arrData['advice_id'])) {
//			if (empty($arrData['advice_id']) || empty($arrData['advice_user_id'])) {
//				throw new SpException('Not set advice_id or advice_user_id.');
//			}
//			$this->addWhere(self::COL_ADVICE_ID, $arrData['advice_id']);
//			$this->addWhere(self::COL_ADVICE_USER_ID, $arrData['advice_user_id']);
//		}
//		if (isset($arrData['consult_id'])) {
//			if (empty($arrData['consult_id']) || empty($arrData['consult_user_id'])) {
//				throw new SpException('Not set consult_id or consult_user_id.');
//			}
//			$this->addWhere(self::COL_CONSULT_ID, $arrData['consult_id']);
//			$this->addWhere(self::COL_CONSULT_USER_ID, $arrData['consult_user_id']);
//		}
//		if (isset($arrData['consult_reply_id'])) {
//			if (empty($arrData['consult_reply_id']) || empty($arrData['consult_reply_user_id'])) {
//				throw new SpException('Not set consult_reply_id or consult_reply_user_id.');
//			}
//			$this->addWhere(self::COL_CONSULT_REPLY_ID, $arrData['consult_reply_id']);
//			$this->addWhere(self::COL_CONSULT_REPLY_USER_ID, $arrData['consult_reply_user_id']);
//		}
//		if (isset($arrData['consult_review_id'])) {
//			if (empty($arrData['consult_review_id']) || empty($arrData['consult_review_user_id'])) {
//				throw new SpException('Not set consult_review_id or consult_review_user_id.');
//			}
//			$this->addWhere(self::COL_CONSULT_REVIEW_ID, $arrData['consult_review_id']);
//			$this->addWhere(self::COL_CONSULT_REVIEW_USER_ID, $arrData['consult_review_user_id']);
//		}
//
//		$feed_id = $this->selectId();
//
//		if ($feed_id > 0) {
//			$this->reset();
//			$this->addValueStr(self::COL_CREATEDATE, $createdate);
//			$this->addWhere(self::COL_FEED_ID, $feed_id);
//			$this->doUpdate();
//		} else {
//			$feed_id = $this->saveStream($user_id, $arrData, $createdate);
//		}
//		return $feed_id;
//	}

	public function getPageList(&$total, $offset, $limit, $user_id)
	{
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addSelect(self::COL_CONSULT_ID);
		$this->addSelect(self::COL_CONSULT_USER_ID);
		$this->addSelect(self::COL_CONSULT_REPLY_ID);
		$this->addSelect(self::COL_CONSULT_REPLY_USER_ID);
		$this->addSelect(self::COL_CONSULT_REVIEW_ID);
		$this->addSelect(self::COL_CONSULT_REVIEW_USER_ID);
		$this->addSelect(self::COL_CREATEDATE);
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
		return $this->selectPage($offset, $limit, $total);
	}

	public function getNewListByIds($user_id)
	{
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addSelect(self::COL_CONSULT_ID);
		$this->addSelect(self::COL_CONSULT_USER_ID);
		$this->addSelect(self::COL_CONSULT_REPLY_ID);
		$this->addSelect(self::COL_CONSULT_REPLY_USER_ID);
		$this->addSelect(self::COL_CONSULT_REVIEW_ID);
		$this->addSelect(self::COL_CONSULT_REVIEW_USER_ID);
		$this->addSelect(self::COL_CREATEDATE);
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
		return $this->select();
	}

//	/**
//	 * 相談窓口
//	 */
//	public function streamAdvice($user_id, $advice_id, $advice_user_id, $createdate)
//	{
//		$this->reset();
//		$this->addValue(self::COL_USER_ID, $user_id);
//		$this->addValue(self::COL_ADVICE_ID, $advice_id);
//		$this->addValue(self::COL_ADVICE_USER_ID, $advice_user_id);
//		$this->addValueStr(self::COL_CREATEDATE, $createdate);
//		$this->doInsert();
//		return $this->getLastInsertId();
//	}
//
//	/**
//	 * 相談
//	 */
//	public function streamConsult($user_id, $advice_id, $advice_user_id, $consult_id, $consult_user_id, $createdate)
//	{
//		$this->reset();
//		$this->addValue(self::COL_USER_ID, $user_id);
//		$this->addValue(self::COL_ADVICE_ID, $advice_id);
//		$this->addValue(self::COL_ADVICE_USER_ID, $advice_user_id);
//		$this->addValue(self::COL_CONSULT_ID, $consult_id);
//		$this->addValue(self::COL_CONSULT_USER_ID, $consult_user_id);
//		$this->addValueStr(self::COL_CREATEDATE, $createdate);
//		$this->doInsert();
//		return $this->getLastInsertId();
//	}
//
//	/**
//	 * 返信
//	 */
//	public function streamReply($user_id, $advice_id, $advice_user_id, $consult_id, $consult_user_id, $reply_id, $reply_user_id, $createdate)
//	{
//		$this->reset();
//		$this->addValue(self::COL_USER_ID, $user_id);
//		$this->addValue(self::COL_ADVICE_ID, $advice_id);
//		$this->addValue(self::COL_ADVICE_USER_ID, $advice_user_id);
//		$this->addValue(self::COL_CONSULT_ID, $consult_id);
//		$this->addValue(self::COL_CONSULT_USER_ID, $consult_user_id);
//		$this->addValue(self::COL_CONSULT_REPLY_ID, $reply_id);
//		$this->addValue(self::COL_CONSULT_REPLY_USER_ID, $reply_user_id);
//		$this->addValueStr(self::COL_CREATEDATE, $createdate);
//		$this->doInsert();
//		return $this->getLastInsertId();
//	}
}
?>