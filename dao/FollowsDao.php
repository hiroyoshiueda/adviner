<?php
/**
 * フォロー管理
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class FollowsDao extends BaseDao
{
	const TABLE_NAME = 'follows';

	const COL_FOLLOW_ID = "follow_id";
	const COL_USER_ID = "user_id";
	const COL_FOLLOW_USER_ID = "follow_user_id";
	const COL_FOLLOW_ADVICE_ID = "follow_advice_id";
	const COL_FOLLOW_ADVICE_USER_ID = "follow_advice_user_id";
	const COL_FOLLOW_MAIN_CATEGORY_ID = "follow_main_category_id";
	const COL_FOLLOW_CATEGORY_ID = "follow_category_id";
	const COL_FOLLOW_TAG = "follow_tag";
	const COL_FOLLOW_WORD = "follow_word";

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
		$this->addWhere(self::COL_FOLLOW_ID, $id);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_FOLLOW_ID, $id);
		return $this->doDelete();
	}

	public function getAdviceList($user_id, $advice_ids=array())
	{
		$this->addSelect(self::COL_FOLLOW_ID);
		$this->addSelect(self::COL_FOLLOW_ADVICE_ID);
		$this->addWhere(self::COL_USER_ID, $user_id);
		if (count($advice_ids) > 0) {
			$this->addWhereIn(self::COL_FOLLOW_ADVICE_ID, $advice_ids);
		}
		$this->addWhere(self::COL_FOLLOW_ADVICE_ID, 0, '>');
		return $this->select();
	}

	public function getUserList($user_id, $user_ids=array())
	{
		$this->addSelect(self::COL_FOLLOW_ID);
		$this->addSelect(self::COL_FOLLOW_USER_ID);
		$this->addWhere(self::COL_USER_ID, $user_id);
		if (count($user_ids) > 0) {
			$this->addWhereIn(self::COL_FOLLOW_USER_ID, $user_ids);
		}
		return $this->select();
	}

	public function getFollowIdByFollowUserId($user_id, $follow_user_id)
	{
		$this->addSelect(self::COL_FOLLOW_ID);
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_FOLLOW_USER_ID, $follow_user_id);
		return $this->selectId();
	}

	public function getFollowIdByFollowAdviceId($user_id, $follow_advice_id)
	{
		$this->addSelect(self::COL_FOLLOW_ID);
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_FOLLOW_ADVICE_ID, $follow_advice_id);
		return $this->selectId();
	}

	/**
	 * （ユーザー）フォロー処理
	 * @param int $user_id
	 * @param int $follow_user_id
	 * @return 0：追加済み、1：追加完了
	 */
	public function addUser($user_id, $follow_user_id)
	{
		$this->addSelectCount(self::COL_FOLLOW_ID, 'total');
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_FOLLOW_USER_ID, $follow_user_id);
		if ($this->selectId() == 0)
		{
			$this->addValue(self::COL_USER_ID, $user_id);
			$this->addValue(self::COL_FOLLOW_USER_ID, $follow_user_id);
			$this->doInsert();
			return 1;
		}
		return 0;
	}

	/**
	 * （ユーザー）フォロー解除処理
	 */
	public function delUser($user_id, $follow_user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_FOLLOW_USER_ID, $follow_user_id);
		return $this->doDelete();
	}

	/**
	 * （相談窓口）フォロー処理
	 */
	public function addAdvice($user_id, $follow_advice_id, $follow_advice_user_id)
	{
		$this->addSelectCount(self::COL_FOLLOW_ID, 'total');
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_FOLLOW_ADVICE_ID, $follow_advice_id);
		if ($this->selectId() == 0)
		{
			$this->addValue(self::COL_USER_ID, $user_id);
			$this->addValue(self::COL_FOLLOW_ADVICE_ID, $follow_advice_id);
			$this->addValue(self::COL_FOLLOW_ADVICE_USER_ID, $follow_advice_user_id);
			return $this->doInsert();
		}
		return true;
	}

	/**
	 * （相談窓口）フォロー解除処理
	 */
	public function delAdvice($user_id, $follow_advice_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_FOLLOW_ADVICE_ID, $follow_advice_id);
		return $this->doDelete();
	}
}
?>