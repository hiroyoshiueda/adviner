<?php
/**
 * アドバイス窓口
 * @author Hiroyoshi
 */
class AdviceRanksDao extends BaseDao
{
	const TABLE_NAME = 'advice_ranks';

const COL_ADVICE_ID = "advice_id";
const COL_ADVICE_USER_ID = "advice_user_id";
const COL_PV_TOTAL = "pv_total";
const COL_PV_TODAY = "pv_today";
const COL_PV_1 = "pv_1";
const COL_PV_2 = "pv_2";
const COL_PV_3 = "pv_3";
const COL_PV_4 = "pv_4";
const COL_PV_5 = "pv_5";
const COL_PV_6 = "pv_6";
const COL_PV_7 = "pv_7";
const COL_CONSULT_TOTAL = "consult_total";
const COL_CONSULT_TODAY = "consult_today";
const COL_CONSULT_1 = "consult_1";
const COL_CONSULT_2 = "consult_2";
const COL_CONSULT_3 = "consult_3";
const COL_CONSULT_4 = "consult_4";
const COL_CONSULT_5 = "consult_5";
const COL_CONSULT_6 = "consult_6";
const COL_CONSULT_7 = "consult_7";

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
		$this->addWhere(self::COL_ADVICE_ID, $id);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_ADVICE_ID, $id);
		return $this->doDelete();
	}

	public function updateCountPv($advice_id)
	{
		$this->addValue(self::COL_PV_TOTAL, self::COL_PV_TOTAL.'+1');
		$this->addValue(self::COL_PV_TODAY, self::COL_PV_TODAY.'+1');
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		return $this->doUpdate();
	}

	public function updateCountConsult($advice_id)
	{
		$this->addValue(self::COL_CONSULT_TOTAL, self::COL_CONSULT_TOTAL.'+1');
		$this->addValue(self::COL_CONSULT_TODAY, self::COL_CONSULT_TODAY.'+1');
		$this->addWhere(self::COL_ADVICE_ID, $advice_id);
		return $this->doUpdate();
	}
}
?>