<?php
/**
 * 支払い管理
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class OrderPaymentsDao extends BaseDao
{
	const TABLE_NAME = 'order_payments';

	const COL_ORDER_PAYMENT_ID = "order_payment_id";
	const COL_DELETE_FLAG = "delete_flag";
	const COL_DISPLAY_FLAG = "display_flag";
	const COL_USER_ID = "user_id";
	const COL_YEAR_MONTHLY = "year_monthly";
	const COL_STATUS = "status";
	const COL_PREV_SALES_TOTAL = "prev_sales_total";
	const COL_PREV_REWARD_TOTAL = "prev_reward_total";
	const COL_SALES_TOTAL = "sales_total";
	const COL_REWARD_TOTAL = "reward_total";
	const COL_BANK_TOTAL = "bank_total";
	const COL_STOCK_TOTAL = "stock_total";
	const COL_CARRY_TOTAL = "carry_total";
	const COL_USER_MESSAGE = "user_message";
	const COL_ERROR_MESSAGE = "error_message";
	const COL_CREATEDATE = "createdate";
	const COL_LASTUPDATE = "lastupdate";

	const STATUS_BEFORE = 1;
	const STATUS_FINISH = 2;
	const STATUS_ERROR = 3;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList()
	{
		return $this->select();
	}

	public function getItem($order_payment_id)
	{
		$this->addWhere(self::COL_ORDER_PAYMENT_ID, $order_payment_id);
		$this->addWhere(self::COL_DELETE_FLAG, parent::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function delete($order_payment_id)
	{
		$this->addWhere(self::COL_ORDER_PAYMENT_ID, $order_payment_id);
		return $this->doDelete();
	}

	public function getItemByYearMonthly($user_id, $year, $month)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhereStr(self::COL_YEAR_MONTHLY, sprintf('%04d%02d', $year, $month));
		$this->addWhere(self::COL_DELETE_FLAG, parent::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function getYearMonthly($user_id)
	{
		$this->addSelect(self::COL_YEAR_MONTHLY);
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_DELETE_FLAG, parent::DELETE_FLAG_ON);
		$this->addOrder(self::COL_YEAR_MONTHLY, 'DESC');
		return $this->select();
	}
}
?>