<?php
/**
 * 問い合わせデータ
 * @author Hiroyoshi
 */
class ContactsDao extends BaseDao
{
	const TABLE_NAME = 'contacts';

const COL_CONTACT_ID = "contact_id";
const COL_STATUS = "status";
const COL_USEREMAIL = "useremail";
const COL_USERNAME = "username";
const COL_SUBJECT = "subject";
const COL_BODY = "body";
const COL_COMPANY_NAME = "company_name";
const COL_DIVISION = "division";
const COL_INDUSTRY = "industry";
const COL_TEL = "tel";
const COL_BUDGET = "budget";
const COL_PRODUCT = "product";
const COL_PRODUCT_URL = "product_url";
const COL_USERINFO = "userinfo";
const COL_USERAGENT = "useragent";
const COL_CREATEDATE = "createdate";

	const STATUS_QUICK = 0;
	const STATUS_BASIC = 1;
	const STATUS_AD = 2;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList($status)
	{
		$this->addWhere(self::COL_STATUS, $status);
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
		return $this->select();
	}

	public function getItem($id)
	{
		$this->addWhere(self::COL_CONTACT_ID, $id);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_CONTACT_ID, $id);
		return $this->doDelete();
	}
}
?>