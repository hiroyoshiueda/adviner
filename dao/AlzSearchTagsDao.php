<?php
/**
 * 検索タグ収集
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class AlzSearchTagsDao extends BaseDao
{
	const TABLE_NAME = 'alz_search_tags';

	const COL_ALZ_SEARCH_TAG_ID = "alz_search_tag_id";
	const COL_SEARCH_TAG = "search_tag";
	//const COL_EXIST_FLAG = "exist_flag";
	const COL_SEARCH_TOTAL = "search_total";
	const COL_CREATEDATE = "createdate";

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
		$this->addWhere(self::COL_ALZ_SEARCH_TAG_ID, $id);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_ALZ_SEARCH_TAG_ID, $id);
		return $this->doDelete();
	}

	public function register($search_tag)
	{
		$search_tag = mb_convert_kana($search_tag, "KVa");

		$this->addSelect(self::COL_ALZ_SEARCH_TAG_ID);
		$this->addWhereStr(self::COL_SEARCH_TAG, $search_tag);
		$id = $this->selectId();
		if ($id == 0) {
			$this->addValueStr(self::COL_SEARCH_TAG, $search_tag);
			$this->doInsert();
			return $this->getLastInsertId();
		} else{
			return $id;
		}
	}

	public function exist($id)
	{
		$this->reset();
		$this->addvalue(self::COL_SEARCH_TOTAL, self::COL_SEARCH_TOTAL.'+1');
		$this->addWhere(self::COL_ALZ_SEARCH_TAG_ID, $id);
		return $this->doUpdate();
	}

	public function getActiveTag($limit)
	{
		$this->addSelect(self::COL_SEARCH_TAG);
		$this->addWhere(self::COL_SEARCH_TOTAL, 0, '>');
		$this->addOrder(self::COL_SEARCH_TOTAL, 'DESC');
		$this->addLimit($limit);
		return $this->select();
	}
}
?>