<?php
/**
 * 未読管理
 * @author Hiroyoshi
 */
class TargetReadsDao extends BaseDao
{
	const TABLE_NAME = 'target_reads';

const COL_TARGET_READ_ID = "target_read_id";
const COL_USER_ID = "user_id";
const COL_TARGET_TYPE = "target_type";
const COL_ADVICE_ID = "advice_id";
const COL_CONSULT_ID = "consult_id";
const COL_CONSULT_REPLY_ID = "consult_reply_id";
const COL_CREATEDATE = "createdate";

	const TARGET_TYPE_CONSULT = 1;
	const TARGET_TYPE_REPLY = 2;
	const TARGET_TYPE_REVIEW = 3;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList()
	{
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
		return $this->select();
	}

	public function getItem($id)
	{
		$this->addWhere(self::COL_TARGET_READ_ID, $id);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_TARGET_READ_ID, $id);
		return $this->doDelete();
	}

	public function registerConsult($user_id, $advice_id, $consult_id)
	{
		$this->addValue(self::COL_USER_ID, $user_id);
		$this->addValue(self::COL_TARGET_TYPE, self::TARGET_TYPE_CONSULT);
		$this->addValue(self::COL_ADVICE_ID, $advice_id);
		$this->addValue(self::COL_CONSULT_ID, $consult_id);
		$this->addValue(self::COL_CREATEDATE, Dao::DATE_NOW);
		return $this->doInsert();
	}

	public function registerReply($user_id, $advice_id, $consult_id, $consult_reply_id)
	{
		$this->addValue(self::COL_USER_ID, $user_id);
		$this->addValue(self::COL_TARGET_TYPE, self::TARGET_TYPE_REPLY);
		$this->addValue(self::COL_ADVICE_ID, $advice_id);
		$this->addValue(self::COL_CONSULT_ID, $consult_id);
		$this->addValue(self::COL_CONSULT_REPLY_ID, $consult_reply_id);
		$this->addValue(self::COL_CREATEDATE, Dao::DATE_NOW);
		return $this->doInsert();
	}

	public function registerReview($user_id, $advice_id, $consult_id)
	{
		$this->addValue(self::COL_USER_ID, $user_id);
		$this->addValue(self::COL_TARGET_TYPE, self::TARGET_TYPE_REVIEW);
		$this->addValue(self::COL_ADVICE_ID, $advice_id);
		$this->addValue(self::COL_CONSULT_ID, $consult_id);
		$this->addValue(self::COL_CREATEDATE, Dao::DATE_NOW);
		return $this->doInsert();
	}

	public function getAllSet($user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$list = $this->select();
		return $list;
	}

	public function getMypageSet($user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
//		$this->addWhere(self::COL_TARGET_TYPE, self::TARGET_TYPE_REPLY);
		$list = $this->select();

		$advice_read = array();
		$consult_read = array();

		if (empty($list) === false)
		{
			foreach ($list as $d)
			{
				if (isset($advice_read[$d['advice_id']]) === false) $advice_read[$d['advice_id']] = array();
				$advice_read[$d['advice_id']][] = $d;
				if (isset($consult_read[$d['consult_id']]) === false) $consult_read[$d['consult_id']] = array();
				$consult_read[$d['consult_id']][] = $d;
			}
		}

		return array($advice_read, $consult_read);
	}

	public function getConsultSet($user_id, $advice_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		$list = $this->select();

		$consult_read = array();
		$reply_read = array();

		if (empty($list) === false)
		{
			foreach ($list as $d)
			{
				if ($d['target_type'] == self::TARGET_TYPE_CONSULT)
				{
					if (isset($consult_read[$d['consult_id']]) === false) $consult_read[$d['consult_id']] = array();
					$consult_read[$d['consult_id']][] = $d;
				}
				else
				{
					if (isset($reply_read[$d['consult_id']]) === false) $reply_read[$d['consult_id']] = array();
					$reply_read[$d['consult_id']][] = $d;
				}
			}
		}

		return array($consult_read, $reply_read);
	}

	public function getReplySet($user_id, $consult_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_CONSULT_ID, $consult_id);
		$list = $this->select();

		$consult_read = array();
		$reply_read = array();
		$review_read = array();

		if (empty($list) === false)
		{
			foreach ($list as $d)
			{
				if ($d['target_type'] == self::TARGET_TYPE_CONSULT)
				{
					if (isset($consult_read[$d['consult_id']]) === false) $consult_read[$d['consult_id']] = array();
					$consult_read[$d['consult_id']][] = $d;
				}
				else if ($d['target_type'] == self::TARGET_TYPE_REPLY)
				{
					if (isset($reply_read[$d['consult_reply_id']]) === false) $reply_read[$d['consult_reply_id']] = array();
					$reply_read[$d['consult_reply_id']][] = $d;
				}
				else if ($d['target_type'] == self::TARGET_TYPE_REVIEW)
				{
					if (isset($review_read[$d['consult_id']]) === false) $review_read[$d['consult_id']] = array();
					$review_read[$d['consult_id']][] = $d;
				}
			}
		}

		return array($consult_read, $reply_read, $review_read);
	}

	public function readConsult($user_id, $consult_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_CONSULT_ID, $consult_id);
		return $this->doDelete();
	}
}
?>