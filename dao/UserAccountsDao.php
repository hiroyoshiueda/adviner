<?php
/**
 * 口座管理
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserAccountsDao extends BaseDao
{
	const TABLE_NAME = 'user_accounts';

	const COL_USER_ACCOUNT_ID = "user_account_id";
	const COL_DELETE_FLAG = "delete_flag";
	const COL_DISPLAY_FLAG = "display_flag";
	const COL_STATUS = "status";
	const COL_USER_ID = "user_id";
	const COL_PAYMENT_TYPE = "payment_type";
	const COL_BANK_NAME = "bank_name";
	const COL_BANK_CODE = "bank_code";
	const COL_BRANCH_NAME = "branch_name";
	const COL_BRANCH_CODE = "branch_code";
	const COL_DEPOSIT_TYPE = "deposit_type";
	const COL_BANK_NUMBER = "bank_number";
	const COL_BANK_HOLDER = "bank_holder";
	const COL_CREATEDATE = "createdate";
	const COL_LASTUPDATE = "lastupdate";

	const DEPOSIT_TYPE_FUTSUU = 1;
	const DEPOSIT_TYPE_TOUZA = 2;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList()
	{
		return $this->select();
	}

	public function getItem($user_account_id)
	{
		$this->addWhere(self::COL_USER_ACCOUNT_ID, $user_account_id);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function delete($user_account_id)
	{
		$this->addWhere(self::COL_USER_ACCOUNT_ID, $user_account_id);
		return $this->doDelete();
	}

	public function getItemByUserId($user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}
}
?>