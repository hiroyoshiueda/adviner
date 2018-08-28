<?php
/**
 * いいね管理
 */
class LikesDao extends BaseDao
{
	const TABLE_NAME = 'likes';

	const COL_LIKE_ID = "like_id";
	const COL_USER_ID = "user_id";
	const COL_ADVICE_ID = "advice_id";
	const COL_ADVICE_USER_ID = "advice_user_id";
	const COL_LIKE_FB_SHARE = "like_fb_share";
	const COL_LIKE_COMMENT = "like_comment";
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
		$this->addWhere(self::COL_LIKE_ID, $id);
		return $this->selectRow();
	}

	public function delete($id, $user_id)
	{
		$this->addWhere(self::COL_LIKE_ID, $id);
		$this->addWhere(self::COL_USER_ID, $user_id);
		return $this->doDelete();
	}
}
?>