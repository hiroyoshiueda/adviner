<?php
/**
 * Q&A質問(Dao)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class QuestionsDao extends BaseDao
{
	const TABLE_NAME = 'questions';

const COL_QUESTION_ID = "question_id";
const COL_DELETE_FLAG = "delete_flag";
const COL_DISPLAY_FLAG = "display_flag";
const COL_QUESTION_USER_ID = "question_user_id";
const COL_CATEGORY_ID = "category_id";
const COL_QUESTION_TITLE = "question_title";
const COL_QUESTION_BODY = "question_body";
const COL_LIMIT_TYPE = "limit_type";
const COL_ANSWER_TOTAL = "answer_total";
const COL_ANSWERDATE = "answerdate";
const COL_CREATEDATE = "createdate";
const COL_LASTUPDATE = "lastupdate";
const COL_DELETEDATE = "deletedate";

	const LIMIT_TYPE_ANYONE = 0;
	const LIMIT_TYPE_ADVISOR = 1;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList()
	{
		$this->addWhere(self::COL_DELETE_FLAG, 0);
		$this->addOrder(self::COL_ORDER_NUM);
		return $this->select();
	}

	public function getItem($id)
	{
		$this->addWhere(self::COL_QUESTION_ID, $id);
		$this->addWhere(self::COL_DELETE_FLAG, parent::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, parent::DISPLAY_FLAG_ON);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_FAQ_ID, $id);
		return $this->doDelete();
	}

	public function getPageList(&$total, $offset, $limit, $orders)
	{
		$this->addWhere(self::COL_DELETE_FLAG, parent::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, parent::DISPLAY_FLAG_ON);
		foreach ($orders as $order_col => $order) {
			$this->addOrder($order_col, $order);
		}
		return $this->selectPage($offset, $limit, $total);
	}

	public function setSearch($keyword, $p='')
	{
		if ($keyword != '') {
			$w  = '( '.$p.self::COL_QUESTION.' LIKE '.$this->quoteString('%'.$keyword.'%');
			$w .= ' OR '.$p.self::COL_ANSWER.' LIKE '.$this->quoteString('%'.$keyword.'%');
			$w .= ' )';
			$this->addWhere('', $w);
		}
	}

	public function getMaxOrderNum()
	{
		$this->addSelectMax(self::COL_ORDER_NUM, 'm');
		$this->addWhere(self::COL_DELETE_FLAG, 0);
		return $this->selectId();
	}

	public function countUpPv($id)
	{
		$this->addValue(self::COL_PV, self::COL_PV.'+1');
		$this->addWhere(self::COL_FAQ_ID, $id);
		return $this->doUpdate();
	}

	public function getListByCategoryId($faq_category_id)
	{
		$this->addWhere(self::COL_FAQ_CATEGORY_ID, $faq_category_id);
		$this->addWhere(self::COL_DELETE_FLAG, parent::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, parent::DISPLAY_FLAG_ON);
		$this->addOrder(self::COL_ORDER_NUM);
		return $this->select();
	}
}
?>