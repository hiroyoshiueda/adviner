<?php
/**
 * ユーザーランク
 * @author Hiroyoshi
 */
class UserRanksDao extends BaseDao
{
	const TABLE_NAME = 'user_ranks';

const COL_USER_ID = "user_id";
const COL_RANK = "rank";
const COL_POINT = "point";
const COL_LEVEL = "level";
const COL_EVALUATE_TOTAL = "evaluate_total";
const COL_EVALUATE_AVE = "evaluate_ave";
const COL_EVALUATE_NUM = "evaluate_num";
const COL_EVALUATE_1 = "evaluate_1";
const COL_EVALUATE_2 = "evaluate_2";
const COL_EVALUATE_3 = "evaluate_3";
const COL_EVALUATE_4 = "evaluate_4";
const COL_EVALUATE_5 = "evaluate_5";
const COL_CONSULT_TOTAL = "consult_total";
const COL_CONSULT_TODAY = "consult_today";
const COL_CONSULT_1 = "consult_1";
const COL_CONSULT_2 = "consult_2";
const COL_CONSULT_3 = "consult_3";
const COL_CONSULT_4 = "consult_4";
const COL_CONSULT_5 = "consult_5";
const COL_CONSULT_6 = "consult_6";
const COL_CONSULT_7 = "consult_7";
const COL_FAVORITE_TOTAL = "favorite_total";
const COL_FAVORITE_TODAY = "favorite_today";
const COL_FAVORITE_1 = "favorite_1";
const COL_FAVORITE_2 = "favorite_2";
const COL_FAVORITE_3 = "favorite_3";
const COL_FAVORITE_4 = "favorite_4";
const COL_FAVORITE_5 = "favorite_5";
const COL_FAVORITE_6 = "favorite_6";
const COL_FAVORITE_7 = "favorite_7";

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList($status)
	{
		return $this->select();
	}

	public function getItem($id)
	{
		$this->addWhere(self::COL_USER_ID, $id);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_USER_ID, $id);
		return $this->doDelete();
	}

	public function getRankList($limit, $offset)
	{
		$this->addSelect('u.*');
		$this->addSelect('ur.'.self::COL_RANK);

		$this->setTable(self::TABLE_NAME, 'ur');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'ur.user_id=u.user_id');

		$this->addWhere(UsersDao::COL_CONSULT_STATUS, UsersDao::CONSULT_STATUS_OK);
		$this->addWhere(UsersDao::COL_STATUS, UsersDao::STATUS_REGULAR);
		$this->addWhere(UsersDao::COL_DISPLAY_FLAG, UsersDao::DISPLAY_FLAG_ON);
		$this->addWhere(UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);

		$this->addOrder(self::COL_RANK);

		$this->addLimit($limit, $offset);

		return $this->select();
	}

	public function updateCountConsult($user_id)
	{
		$this->addValue(self::COL_CONSULT_TOTAL, self::COL_CONSULT_TOTAL.'+1');
		$this->addValue(self::COL_CONSULT_TODAY, self::COL_CONSULT_TODAY.'+1');
		$this->addWhere(self::COL_USER_ID, $user_id);
		return $this->doUpdate();
	}
}
?>