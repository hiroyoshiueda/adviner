<?php
/**
 * 評価レビュー
 * @author Hiroyoshi
 */
class ConsultReviewsDao extends BaseDao
{
	const TABLE_NAME = 'consult_reviews';

const COL_CONSULT_REVIEW_ID = "consult_review_id";
const COL_DELETE_FLAG = "delete_flag";
const COL_DISPLAY_FLAG = "display_flag";
const COL_REVIEW_STATUS = "review_status";
const COL_CONSULT_REVIEW_USER_ID = "consult_review_user_id";
const COL_ADVICE_ID = "advice_id";
const COL_ADVICE_USER_ID = "advice_user_id";
const COL_CONSULT_ID = "consult_id";
const COL_CONSULT_USER_ID = "consult_user_id";
const COL_CONSULT_PUBLIC_FLAG = "consult_public_flag";
const COL_REVIEW_PUBLIC_FLAG = "review_public_flag";
const COL_SECRET_FLAG = "secret_flag";
const COL_REVIEW_SHARE = "review_share";
const COL_EVALUATE_TYPE = "evaluate_type";
const COL_REVIEW_BODY = "review_body";
const COL_CREATEDATE = "createdate";
const COL_LASTUPDATE = "lastupdate";
const COL_DELETEDATE = "deletedate";

	const CONSULT_PUBLIC_FLAG_PRIVATE = 1;
	const CONSULT_PUBLIC_FLAG_PUBLIC = 2;

	const REVIEW_PUBLIC_FLAG_PRIVATE = 1;
	const REVIEW_PUBLIC_FLAG_PUBLIC = 2;

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
		$this->addWhere(self::COL_CONSULT_REVIEW_ID, $id);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_USER_ID, $id);
		return $this->doDelete();
	}

	public function getItemOnMypageConsult($consult_id, $consult_user_id)
	{
		$this->addWhere(self::COL_CONSULT_ID, $consult_id);
		$this->addWhere(self::COL_CONSULT_USER_ID, $consult_user_id);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addLimit(1);
		return $this->selectRow();
	}

	/**
	 * 公開用評価コメント
	 * ※削除ユーザーも匿名扱いでコメントは表示
	 */
//	public function getListOnPublic($advice_id, $advice_user_id)
//	{
//		$this->addSelect('cr.'.self::COL_CONSULT_REVIEW_ID);
//		$this->addSelect('cr.'.self::COL_ADVICE_ID);
//		$this->addSelect('cr.'.self::COL_CONSULT_ID);
//		$this->addSelect('cr.'.self::COL_CONSULT_PUBLIC_FLAG);
//		$this->addSelect('cr.'.self::COL_SECRET_FLAG);
//		$this->addSelect('cr.'.self::COL_REVIEW_SHARE);
//		$this->addSelect('cr.'.self::COL_EVALUATE_TYPE);
//		$this->addSelect('cr.'.self::COL_REVIEW_BODY);
//		$this->addSelect('cr.'.self::COL_CREATEDATE);
//		$this->addSelect('u.'.UsersDao::COL_USER_ID);
//		$this->addSelect('u.'.UsersDao::COL_DELETE_FLAG);
//		$this->addSelect('u.'.UsersDao::COL_DISPLAY_FLAG);
//		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
//		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);
//
//		$this->setTable(self::TABLE_NAME, 'cr');
//		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'cr.consult_user_id=u.user_id');
//
//		$this->addWhere('cr.'.self::COL_ADVICE_ID, $advice_id);
//		$this->addWhere('cr.'.self::COL_ADVICE_USER_ID, $advice_user_id);
//		$this->addWhere('cr.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
//		$this->addWhere('cr.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
//		$this->addOrder('cr.'.self::COL_CREATEDATE, 'DESC');
//		return $this->select();
//	}
	public function getListOnPublic($advice_id, $consult_ids)
	{
		$this->addSelect('cr.'.self::COL_CONSULT_REVIEW_ID);
		$this->addSelect('cr.'.self::COL_ADVICE_ID);
		$this->addSelect('cr.'.self::COL_CONSULT_ID);
		$this->addSelect('cr.'.self::COL_CONSULT_PUBLIC_FLAG);
		$this->addSelect('cr.'.self::COL_REVIEW_PUBLIC_FLAG);
		$this->addSelect('cr.'.self::COL_SECRET_FLAG);
		$this->addSelect('cr.'.self::COL_REVIEW_SHARE);
		$this->addSelect('cr.'.self::COL_EVALUATE_TYPE);
		$this->addSelect('cr.'.self::COL_REVIEW_BODY);
		$this->addSelect('cr.'.self::COL_CREATEDATE);
		$this->addSelect('u.'.UsersDao::COL_USER_ID);
		$this->addSelect('u.'.UsersDao::COL_DELETE_FLAG);
		$this->addSelect('u.'.UsersDao::COL_DISPLAY_FLAG);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);

		$this->setTable(self::TABLE_NAME, 'cr');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'cr.consult_user_id=u.user_id');

		$this->addWhere('cr.'.self::COL_ADVICE_ID, $advice_id);
		$this->addWhereIn('cr.'.self::COL_CONSULT_ID, $consult_ids);
		$this->addWhere('cr.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('cr.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		//$this->addOrder('cr.'.self::COL_CREATEDATE, 'DESC');
		return $this->select();
	}

//	public function getListOfPublicOnly($advice_id)
//	{
//		$this->addSelect(self::COL_CONSULT_REVIEW_ID);
//		$this->addSelect(self::COL_REVIEW_STATUS);
//		$this->addSelect(self::COL_CONSULT_REVIEW_USER_ID);
//		$this->addSelect(self::COL_ADVICE_ID);
//		$this->addSelect(self::COL_ADVICE_USER_ID);
//		$this->addSelect(self::COL_CONSULT_ID);
//		$this->addSelect(self::COL_CONSULT_USER_ID);
//		$this->addSelect(self::COL_CONSULT_PUBLIC_FLAG);
//		$this->addSelect(self::COL_REVIEW_PUBLIC_FLAG);
//		$this->addSelect(self::COL_SECRET_FLAG);
//		$this->addSelect(self::COL_REVIEW_SHARE);
//		$this->addSelect(self::COL_EVALUATE_TYPE);
//		$this->addSelect(self::COL_REVIEW_BODY);
//		$this->addSelect(self::COL_CREATEDATE);
//
//		$consult_ids = array_unique($consult_ids);
//		$cnt = count($consult_ids);
//		if ($cnt > 1) {
//			$this->addWhereIn(self::COL_CONSULT_ID, $consult_ids);
//		} else if ($cnt == 1) {
//			$this->addWhere(self::COL_CONSULT_ID, $consult_ids[0]);
//		} else {
//			return array();
//		}
//		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
//		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
//		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
//		//$this->addOrder(self::COL_CREATEDATE);
//		return $this->select();
//	}

	/**
	 * @param array $consult_ids
	 */
	public function getListByConsultIds($consult_ids)
	{
		$this->addSelect(self::COL_CONSULT_REVIEW_ID);
		$this->addSelect(self::COL_REVIEW_STATUS);
		$this->addSelect(self::COL_CONSULT_REVIEW_USER_ID);
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addSelect(self::COL_CONSULT_ID);
		$this->addSelect(self::COL_CONSULT_USER_ID);
		$this->addSelect(self::COL_CONSULT_PUBLIC_FLAG);
		$this->addSelect(self::COL_REVIEW_PUBLIC_FLAG);
		$this->addSelect(self::COL_SECRET_FLAG);
		$this->addSelect(self::COL_REVIEW_SHARE);
		$this->addSelect(self::COL_EVALUATE_TYPE);
		$this->addSelect(self::COL_REVIEW_BODY);
		$this->addSelect(self::COL_CREATEDATE);

		$consult_ids = array_unique($consult_ids);
		$cnt = count($consult_ids);
		if ($cnt > 1) {
			$this->addWhereIn(self::COL_CONSULT_ID, $consult_ids);
		} else if ($cnt == 1) {
			$this->addWhere(self::COL_CONSULT_ID, $consult_ids[0]);
		} else {
			return array();
		}
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		//$this->addOrder(self::COL_CREATEDATE);
		return $this->select();
	}

	/**
	 * @param array $consult_review_ids
	 */
	public function getReviewList($consult_review_ids)
	{
		$this->addSelect(self::COL_CONSULT_REVIEW_ID);
		$this->addSelect(self::COL_REVIEW_STATUS);
		$this->addSelect(self::COL_CONSULT_REVIEW_USER_ID);
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addSelect(self::COL_CONSULT_ID);
		$this->addSelect(self::COL_CONSULT_USER_ID);
		$this->addSelect(self::COL_CONSULT_PUBLIC_FLAG);
		$this->addSelect(self::COL_REVIEW_PUBLIC_FLAG);
		$this->addSelect(self::COL_SECRET_FLAG);
		$this->addSelect(self::COL_REVIEW_SHARE);
		$this->addSelect(self::COL_EVALUATE_TYPE);
		$this->addSelect(self::COL_REVIEW_BODY);
		$this->addSelect(self::COL_CREATEDATE);

		$consult_review_ids = array_unique($consult_review_ids);
		$cnt = count($consult_review_ids);
		if ($cnt > 1) {
			$this->addWhereIn(self::COL_CONSULT_REVIEW_ID, $consult_review_ids);
		} else if ($cnt == 1) {
			$this->addWhere(self::COL_CONSULT_REVIEW_ID, $consult_review_ids[0]);
		} else {
			return array();
		}
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->select();
	}

	/**
	 * Feed用
	 * @see FeedStream.php
	 */
	public function getFeedListByFollowData(&$followData, $latestDate, $limit, $is_all=false)
	{
		$this->addSelect('crv.'.self::COL_CONSULT_REVIEW_ID);
		$this->addSelect('crv.'.self::COL_CONSULT_REVIEW_USER_ID);
		$this->addSelect('crv.'.self::COL_ADVICE_ID);
		$this->addSelect('crv.'.self::COL_ADVICE_USER_ID);
		$this->addSelect('crv.'.self::COL_CONSULT_ID);
		$this->addSelect('crv.'.self::COL_CONSULT_USER_ID);
		$this->addSelect('crv.'.self::COL_CREATEDATE);

		$this->setTable(self::TABLE_NAME, 'crv');
		$this->addTableJoin(ConsultsDao::TABLE_NAME, 'c', 'crv.consult_id=c.consult_id');

		if (empty($latestDate) === false)
		{
			$this->addWhereStr('crv.'.self::COL_CREATEDATE, $latestDate, '>');
		}
		if ($is_all === false)
		{
			if (empty($followData['user_id']) && empty($followData['advice_id'])) return array();

			$wh1 = '';
			if (empty($followData['user_id']) === false)
			{
				$wh1 = 'crv.'.self::COL_CONSULT_REVIEW_USER_ID.' IN ('.implode(',', $followData['user_id']).')';
			}

			$wh2 = '';
			if (empty($followData['advice_id']) === false)
			{
				$wh2 = 'crv.'.self::COL_ADVICE_ID.' IN ('.implode(',', $followData['advice_id']).')';
			}

			if ($wh1 != '' && $wh2 != '') {
				$this->addWhere('', '('.$wh1.' OR '.$wh2.')');
			} else if ($wh1 != '') {
				$this->addWhere('', $wh1);
			} else if ($wh2 != '') {
				$this->addWhere('', $wh2);
			}
		}

		$this->addWhere('crv.'.self::COL_REVIEW_PUBLIC_FLAG, self::REVIEW_PUBLIC_FLAG_PUBLIC);
		$this->addWhere('crv.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('crv.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		//$this->addWhere('c.'.ConsultsDao::COL_LATEST_REPLY_ID, 0, '>');
		$this->addWhere('c.'.ConsultsDao::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.ConsultsDao::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addLimit($limit);

		return $this->select();
	}
}
?>