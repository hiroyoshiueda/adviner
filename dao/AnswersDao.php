<?php
/**
 * Q&A回答(Dao)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class AnswersDao extends BaseDao
{
	const TABLE_NAME = 'answers';

const COL_ANSWER_ID = "answer_id";
const COL_DELETE_FLAG = "delete_flag";
const COL_DISPLAY_FLAG = "display_flag";
const COL_QUESTION_ID = "question_id";
const COL_QUESTION_USER_ID = "question_user_id";
const COL_ANSWER_USER_ID = "answer_user_id";
const COL_ANSWER_TITLE = "answer_title";
const COL_ANSWER_BODY = "answer_body";
const COL_BEST_ANSWER_FLAG = "best_answer_flag";
const COL_CREATEDATE = "createdate";
const COL_LASTUPDATE = "lastupdate";
const COL_DELETEDATE = "deletedate";

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
		$this->addWhere(self::COL_ANSWER_ID, $id);
		$this->addWhere(self::COL_DELETE_FLAG, parent::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, parent::DISPLAY_FLAG_ON);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_FAQ_ID, $id);
		return $this->doDelete();
	}

	public function getPageList(&$total, $offset, $limit, $order_col='', $order='')
	{
		$this->addWhere(self::COL_DELETE_FLAG, 0);
		if ($order_col != '') {
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