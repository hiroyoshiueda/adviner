<?php
/**
 * 注文管理
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class OrdersDao extends BaseDao
{
	const TABLE_NAME = 'orders';

	const COL_ORDER_ID = "order_id";
	const COL_DELETE_FLAG = "delete_flag";
	const COL_DISPLAY_FLAG = "display_flag";
	const COL_STATUS = "status";
	const COL_USER_ID = "user_id";
	const COL_ADVICE_ID = "advice_id";
	const COL_ADVICE_USER_ID = "advice_user_id";
	const COL_CONSULT_ID = "consult_id";
	const COL_CONSULT_USER_ID = "consult_user_id";
	const COL_PAYMENT_METHOD = "payment_method";
	const COL_AMOUNT = "amount";
	const COL_PAYMENT_FEE = "payment_fee";
	const COL_PAYMENT_KEY = "payment_key";
	const COL_PAYMENT_DATA = "payment_data";
	const COL_REWARD = "reward";
	const COL_USER_CHARGE_RATE = "user_charge_rate";
	const COL_CREATEDATE = "createdate";
	const COL_FINISHDATE = "finishdate";
	const COL_DELETEDATE = "deletedate";
	const COL_ORDER_NAME = "order_name";
	const COL_ORDER_FIRST_NAME = "order_first_name";
	const COL_ORDER_LAST_NAME = "order_last_name";
	const COL_ORDER_EMAIL = "order_email";
	const COL_ORDER_PHONE = "order_phone";
	const COL_ORDER_ZIP = "order_zip";
	const COL_ORDER_ADDRESS1 = "order_address1";
	const COL_ORDER_ADDRESS2 = "order_address2";
	const COL_ORDER_ADDRESS3 = "order_address3";
	const COL_ORDER_ADDRESS4 = "order_address4";

	const STATUS_PROG = 1;
	const STATUS_RECEIVE = 2;
	const STATUS_CANCEL = 3;

	const PAYMENT_METHOD_CARD = 1;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList()
	{
		return $this->select();
	}

	public function getItem($order_id)
	{
		$this->addWhere(self::COL_ORDER_ID, $order_id);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function delete($order_id)
	{
		$this->addWhere(self::COL_ORDER_ID, $order_id);
		return $this->doDelete();
	}

	public function getItemByAdviceIdAndConsultId($order_id, $advice_id, $consult_id)
	{
		$this->addWhere(self::COL_ORDER_ID, $order_id);
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		$this->addWhere(self::COL_CONSULT_ID, $consult_id);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function statusCancel($order_id)
	{
		$this->addValue(self::COL_STATUS, self::STATUS_CANCEL);
		$this->addValue(self::COL_FINISHDATE, Dao::DATE_NOW);
		$this->addWhere(self::COL_ORDER_ID, $order_id);
		return $this->doUpdate();
	}

	public function getOwnerPageList(&$total, $offset, $limit, $user_id, $sdate, $edate)
	{
		$this->addWhere(self::COL_ADVICE_USER_ID, $user_id);
		if ($sdate !== null) $this->addWhereStr(self::COL_CREATEDATE, $sdate, '>=');
		if ($edate !== null) $this->addWhereStr(self::COL_CREATEDATE, $edate, '<=');
		$this->addWhere(self::COL_STATUS, self::STATUS_RECEIVE);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
		return $this->selectPage($offset, $limit, $total);
	}

	public function getPurchaserPageList(&$total, $offset, $limit, $user_id)
	{
		$this->addWhere(self::COL_CONSULT_USER_ID, $user_id);
		$this->addWhere(self::COL_STATUS, self::STATUS_RECEIVE);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
		return $this->selectPage($offset, $limit, $total);
	}
}
?>