<?php
/**
 * 相談履歴
 * @author Hiroyoshi
 */
class ConsultReplysDao extends BaseDao
{
	const TABLE_NAME = 'consult_replys';

	const COL_CONSULT_REPLY_ID = "consult_reply_id";
	const COL_DELETE_FLAG = "delete_flag";
	const COL_DISPLAY_FLAG = "display_flag";
	const COL_REPLY_STATUS = "reply_status";
	const COL_ADVICE_ID = "advice_id";
	const COL_ADVICE_USER_ID = "advice_user_id";
	const COL_CONSULT_ID = "consult_id";
	const COL_CONSULT_USER_ID = "consult_user_id";
	const COL_CONSULT_PUBLIC_FLAG = "consult_public_flag";
	const COL_FROM_USER_ID = "from_user_id";
	const COL_TO_USER_ID = "to_user_id";
	const COL_REPLY_OPT = "reply_opt";
	const COL_REPLY_BODY = "reply_body";
	const COL_FINISH_FLAG = "finish_flag";
	const COL_ADVICE_CHARGE_FLAG = "advice_charge_flag";
	const COL_CREATEDATE = "createdate";
	const COL_LASTUPDATE = "lastupdate";
	const COL_DELETEDATE = "deletedate";

	const REPLY_STATUS_CONSULT = 1;
	const REPLY_STATUS_ADVISOR = 11;

	const REPLY_OPT_NORMAL = 0;
	const REPLY_OPT_ADVICE = 1;
	const REPLY_OPT_QUESTION = 2;
	const REPLY_OPT_NOADVICE = 3;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList()
	{
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
		return $this->select();
	}

	public function getItem($id)
	{
		$this->addWhere(self::COL_COMMENT_ID, $id);
		return $this->selectRow();
	}

	public function delete($id, $user_id=0)
	{
		$this->addWhere(self::COL_COMMENT_ID, $id);
		if ($user_id>0) $this->addWhere(self::COL_USER_ID, $user_id);
		return $this->doDelete();
	}

	public function getListByConsultIds($consult_ids)
	{
		$this->addSelect(self::COL_CONSULT_REPLY_ID);
		$this->addSelect(self::COL_REPLY_STATUS);
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addSelect(self::COL_CONSULT_ID);
		$this->addSelect(self::COL_CONSULT_USER_ID);
		$this->addSelect(self::COL_CONSULT_PUBLIC_FLAG);
		$this->addSelect(self::COL_FROM_USER_ID);
		$this->addSelect(self::COL_TO_USER_ID);
		$this->addSelect(self::COL_REPLY_OPT);
		$this->addSelect(self::COL_REPLY_BODY);
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
		$this->addOrder(self::COL_CONSULT_REPLY_ID);
		return $this->select();
	}

	public function getItemOfMypageFromUser($consult_reply_id)
	{
		$this->addSelect('cr.'.self::COL_CONSULT_REPLY_ID);
		$this->addSelect('cr.'.self::COL_ADVICE_ID);
		$this->addSelect('cr.'.self::COL_ADVICE_USER_ID);
		$this->addSelect('cr.'.self::COL_CONSULT_ID);
		$this->addSelect('cr.'.self::COL_CONSULT_USER_ID);
		$this->addSelect('cr.'.self::COL_CONSULT_PUBLIC_FLAG);
		$this->addSelect('cr.'.self::COL_FROM_USER_ID);
		$this->addSelect('cr.'.self::COL_REPLY_BODY);
		$this->addSelect('cr.'.self::COL_CREATEDATE);
		$this->addSelect('u.'.UsersDao::COL_LOGIN);
		$this->addSelect('u.'.UsersDao::COL_NICKNAME);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_S_PATH);
		$this->addSelect('u.'.UsersDao::COL_PROFILE_B_PATH);

		$this->setTable(self::TABLE_NAME, 'cr');
		$this->addTableJoin(UsersDao::TABLE_NAME, 'u', 'cr.from_user_id=u.user_id');

		$this->addWhere('cr.'.self::COL_CONSULT_REPLY_ID, $consult_reply_id);
		$this->addWhere('cr.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('cr.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere('u.'.UsersDao::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		return $this->selectRow();
	}

	/**
	 * @param array $consult_reply_ids
	 */
	public function getReplyList($consult_reply_ids)
	{
		$this->addSelect(self::COL_CONSULT_REPLY_ID);
		$this->addSelect(self::COL_REPLY_STATUS);
		$this->addSelect(self::COL_ADVICE_ID);
		$this->addSelect(self::COL_ADVICE_USER_ID);
		$this->addSelect(self::COL_CONSULT_ID);
		$this->addSelect(self::COL_CONSULT_USER_ID);
		$this->addSelect(self::COL_CONSULT_PUBLIC_FLAG);
		$this->addSelect(self::COL_FROM_USER_ID);
		$this->addSelect(self::COL_TO_USER_ID);
		$this->addSelect(self::COL_REPLY_OPT);
		$this->addSelect(self::COL_REPLY_BODY);
		$this->addSelect(self::COL_CREATEDATE);

		$consult_reply_ids = array_unique($consult_reply_ids);
		$cnt = count($consult_reply_ids);
		if ($cnt > 1) {
			$this->addWhereIn(self::COL_CONSULT_REPLY_ID, $consult_reply_ids);
		} else if ($cnt == 1) {
			$this->addWhere(self::COL_CONSULT_REPLY_ID, $consult_reply_ids[0]);
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
		$this->addSelect('cr.'.self::COL_CONSULT_REPLY_ID);
		$this->addSelect('cr.'.self::COL_ADVICE_ID);
		$this->addSelect('cr.'.self::COL_ADVICE_USER_ID);
		$this->addSelect('cr.'.self::COL_CONSULT_ID);
		$this->addSelect('cr.'.self::COL_CONSULT_USER_ID);
		$this->addSelect('cr.'.self::COL_FROM_USER_ID);
		$this->addSelect('cr.'.self::COL_TO_USER_ID);
		$this->addSelectAs('cr.'.self::COL_FROM_USER_ID, 'consult_reply_user_id');
		$this->addSelect('cr.'.self::COL_CREATEDATE);

		$this->setTable(self::TABLE_NAME, 'cr');
		$this->addTableJoin(ConsultsDao::TABLE_NAME, 'c', 'cr.consult_id=c.consult_id');

		if (empty($latestDate) === false)
		{
			$this->addWhereStr('cr.'.self::COL_CREATEDATE, $latestDate, '>');
		}

		if ($is_all === false)
		{
			if (empty($followData['user_id']) && empty($followData['advice_id'])) return array();

			$wh1 = '';
			if (empty($followData['user_id']) === false)
			{
				$wh1 = 'cr.'.self::COL_FROM_USER_ID.' IN ('.implode(',', $followData['user_id']).') OR cr.'.self::COL_TO_USER_ID.' IN ('.implode(',', $followData['user_id']).')';
			}

			$wh2 = '';
			if (empty($followData['advice_id']) === false)
			{
				$wh2 = 'cr.'.self::COL_ADVICE_ID.' IN ('.implode(',', $followData['advice_id']).')';
			}

			if ($wh1 != '' && $wh2 != '') {
				$this->addWhere('', '('.$wh1.' OR '.$wh2.')');
			} else if ($wh1 != '') {
				$this->addWhere('', '('.$wh1.')');
			} else if ($wh2 != '') {
				$this->addWhere('', $wh2);
			}
		}

		$this->addWhere('cr.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('cr.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere('c.'.ConsultsDao::COL_LATEST_REPLY_ID, 0, '>');
		$this->addWhere('c.'.ConsultsDao::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.ConsultsDao::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		$this->addLimit($limit);

		return $this->select();
	}

	/**
	 * キーワード検索用
	 */
	public function getPageListOnSearch(&$total, $offset, $limit, $orderby, $words)
	{
		$this->addSelect('cr.*');
		$this->addSelect('c.'.ConsultsDao::COL_CONSULT_BODY);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TITLE);

		$this->setTable(self::TABLE_NAME, 'cr');
		$this->addTableJoin(ConsultsDao::TABLE_NAME, 'c', 'cr.consult_id=c.consult_id');
		$this->addTableJoin(AdvicesDao::TABLE_NAME, 'a', 'cr.advice_id=a.advice_id');

		if (is_array($words))
		{
			foreach ($words as $word) {
				$wh  = '(cr.'.self::COL_REPLY_BODY.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ' OR c.'.ConsultsDao::COL_CONSULT_BODY.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ')';
				$this->addWhere('', $wh);
			}
		}

		$this->addWhere('cr.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('cr.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere('c.'.ConsultsDao::COL_LATEST_REPLY_ID, 0, '>');
		$this->addWhere('c.'.ConsultsDao::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('c.'.ConsultsDao::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		if (is_array($orderby))
		{
			foreach ($orderby as $col => $order) {
				$this->addOrder($col, $order);
			}
		}

		return $this->selectPage($offset, $limit, $total);
	}
}
?>