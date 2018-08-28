<?php
/**
 * GOOD管理
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class GoodsDao extends BaseDao
{
	const TABLE_NAME = 'goods';

	const COL_GOOD_ID = "good_id";
	const COL_USER_ID = "user_id";
	const COL_PERMALINK = "permalink";
	const COL_ADVICE_ID = "advice_id";
	const COL_CONSULT_ID = "consult_id";
	const COL_CONSULT_REPLY_ID = "consult_reply_id";
	const COL_CONSULT_REVIEW_ID = "consult_review_id";
	const COL_COMMENT_ID = "comment_id";
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

	public function getItem($id)
	{
		$this->addWhere(self::COL_GOOD_ID, $id);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_GOOD_ID, $id);
		return $this->doDelete();
	}

	/**
	 * カウント数を取得
	 * @param string $permalink
	 */
	public function getCount($permalink)
	{
		$this->addSelectCount(self::COL_GOOD_ID, 'total');
		$this->addWhereStr(self::COL_PERMALINK, $permalink);
		return $this->selectId();
	}

	/**
	 * ユーザーのカウント数を取得
	 * @param string $permalink
	 */
	public function getUserCount($user_id, $permalink)
	{
		$this->addSelectCount(self::COL_GOOD_ID, 'total');
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhereStr(self::COL_PERMALINK, $permalink);
		return $this->selectId();
	}

	public function cancel($user_id, $permalink)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhereStr(self::COL_PERMALINK, $permalink);
		return $this->doDelete();
	}

	public function loadGoodData($user_id)
	{
		$this->reset();
		$this->addSelect(self::COL_PERMALINK);
		$this->addSelectCount(self::COL_USER_ID, 'total');
		$this->addGroupBy(self::COL_PERMALINK);
		$list = $this->select();
		$good_count = array();
		if (count($list) > 0)
		{
			foreach ($list as $d)
			{
				$good_count[$d['permalink']] = $d['total'];
			}
		}
	}
}
?>