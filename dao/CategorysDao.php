<?php
/**
 * カテゴリー管理
 * @author Hiroyoshi
 */
class CategorysDao extends BaseDao
{
	const TABLE_NAME = 'categorys';

const COL_CATEGORY_ID = "category_id";
const COL_DELETE_FLAG = "delete_flag";
const COL_DISPLAY_FLAG = "display_flag";
const COL_ORDER_NUM = "order_num";
const COL_MAIN_CATEGORY_ID = "main_category_id";
const COL_CNAME = "cname";
const COL_TOTAL = "total";

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getListByUser($user_id)
	{
		$this->addWhere(self::COL_DELETE_FLAG, 0);
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addOrder(self::COL_LASTUPDATE, 'DESC');
		return $this->select();
	}

	public function getItem($id, $user_id=0)
	{
		$this->addWhere(self::COL_DELETE_FLAG, 0);
		$this->addWhere(self::COL_PUBLICATION_ID, $id);
		if ($user_id>0) $this->addWhere(self::COL_USER_ID, $user_id);
		return $this->selectRow();
	}

	public function delete($id, $user_id)
	{
		$this->addWhere(self::COL_PUBLICATION_ID, $id);
		$this->addWhere(self::COL_USER_ID, $user_id);
		return $this->doDelete();
	}

	public function getKeySet()
	{
		$this->addSelect(self::COL_CATEGORY_ID);
		$this->addSelect(self::COL_MAIN_CATEGORY_ID);
		$this->addSelect(self::COL_CNAME);
		$this->addSelect(self::COL_TOTAL);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addOrder(self::COL_ORDER_NUM);
		$list = $this->select();
		$cate = array();
		if (count($list)>0) {
			foreach ($list as $d) {
				$cate[$d['category_id']] = $d;
			}
		}
		return $cate;
	}

	public function getSelectOptions()
	{
		$this->addSelect(self::COL_CATEGORY_ID);
		$this->addSelect(self::COL_MAIN_CATEGORY_ID);
		$this->addSelect(self::COL_CNAME);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addOrder(self::COL_ORDER_NUM);
		$list = $this->select();
		$opts = array();
		if (count($list)>0) {
			foreach ($list as $d) {
				if (isset($opts[$d['main_category_id']]) === false) $opts[$d['main_category_id']] = array();
				$opts[$d['main_category_id']][] = array(
					'value' => $d['category_id'],
					'text' => $d['cname']
				);
			}
		}
		return $opts;
	}

	public function updateCountUpTotal($category_id)
	{
		$this->reset();
		$this->addValue(self::COL_TOTAL, self::COL_TOTAL.'+1');
		$this->addWhere(self::COL_CATEGORY_ID, $category_id);
		return $this->doUpdate();
	}

	public function updateCountDownTotal($category_id)
	{
		$this->reset();
		$this->addValue(self::COL_TOTAL, self::COL_TOTAL.'-1');
		$this->addWhere(self::COL_CATEGORY_ID, $category_id);
		return $this->doUpdate();
	}

	public function getListByMainCategoryId($main_category_id)
	{
		$this->addSelect(self::COL_CATEGORY_ID);
		$this->addSelect(self::COL_MAIN_CATEGORY_ID);
		$this->addSelect(self::COL_CNAME);
		$this->addWhere(self::COL_MAIN_CATEGORY_ID, $main_category_id);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addOrder(self::COL_ORDER_NUM);
		return $this->select();
	}
}
?>