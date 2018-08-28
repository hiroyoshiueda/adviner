<?php
/**
 * よくある質問カテゴリ(Dao)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class FaqCategorysDao extends BaseDao
{
	const TABLE_NAME = 'faq_categorys';

	const COL_FAQ_CATEGORY_ID = "faq_category_id";
	const COL_DELETE_FLAG = "delete_flag";
	const COL_DISPLAY_FLAG = "display_flag";
	const COL_ORDER_NUM = "order_num";
	const COL_TITLE = "title";
	const COL_REMARKS = "remarks";
	const COL_CREATEDATE = "createdate";
	const COL_LASTUPDATE = "lastupdate";
	const COL_DELETEDATE = "deletedate";

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList()
	{
		$this->addWhere(self::COL_DELETE_FLAG, parent::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, parent::DISPLAY_FLAG_ON);
		$this->addOrder(self::COL_ORDER_NUM);
		return $this->select();
	}

	public function getItem($id)
	{
		$this->addWhere(self::COL_FAQ_CATEGORY_ID, $id);
		$this->addWhere(self::COL_DELETE_FLAG, 0);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addValue(self::COL_DELETE_FLAG, 1);
		$this->addValue(self::COL_DELETEDATE, Dao::DATE_NOW);
		$this->addWhere(self::COL_FAQ_CATEGORY_ID, $id);
		return $this->doUpdate();
	}

	public function getMaxOrderNum()
	{
		$this->addSelectMax(self::COL_ORDER_NUM, 'm');
		$this->addWhere(self::COL_DELETE_FLAG, 0);
		return $this->selectId();
	}

	public function getKeyNameList($is_display=true)
	{
		$this->addWhere(self::COL_DELETE_FLAG, 0);
		if ($is_display) {
			//$this->addOrder(self::COL_DISPLAY_FLAG);
		} else {
			$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_SHOW);
		}
		$this->addOrder(self::COL_ORDER_NUM);
		$list = $this->select();

		$arr = array();
		if (count($list)>0) {
			foreach ($list as $d) {
				$name = $d[self::COL_TITLE];
				if ($d[self::COL_DISPLAY_FLAG] == 1) $name .= "（非表示）";
				$arr[$d[self::COL_FAQ_CATEGORY_ID]] = $name;
			}
		}
		return $arr;
	}
}
?>