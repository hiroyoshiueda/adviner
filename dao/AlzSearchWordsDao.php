<?php
/**
 * 検索キーワード収集
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class AlzSearchWordsDao extends BaseDao
{
	const TABLE_NAME = 'alz_search_words';

	const COL_ALZ_SEARCH_WORD_ID = "alz_search_word_id";
	const COL_SEARCH_WORD = "search_word";
	const COL_SEARCH_OPT = "search_opt";
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
		$this->addWhere(self::COL_ALZ_SEARCH_WORD_ID, $id);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addWhere(self::COL_ALZ_SEARCH_WORD_ID, $id);
		return $this->doDelete();
	}

	public function register($search_word, $search_type=0)
	{
		$search_word = mb_convert_kana($search_word, "KVa");
		$opt_str = ($search_type>0) ? serialize(array('search_type'=>$search_type)) : '';

		$this->addSelect(self::COL_ALZ_SEARCH_WORD_ID);
		$this->addWhereStr(self::COL_SEARCH_WORD, $search_word);
		if ($search_type>0) $this->addWhereStr(self::COL_SEARCH_OPT, $opt_str);
		else $this->addWhereStr(self::COL_SEARCH_OPT, null);
		$id = $this->selectId();
		if ($id == 0) {
			$this->addValueStr(self::COL_SEARCH_WORD, $search_word);
			if ($search_type>0) $this->addValueStr(self::COL_SEARCH_OPT, $opt_str);
			$this->doInsert();
			return $this->getLastInsertId();
		} else {
			return $id;
		}
	}

	public function exist($id)
	{
		$this->reset();
		$this->addvalue(self::COL_SEARCH_TOTAL, self::COL_SEARCH_TOTAL.'+1');
		$this->addWhere(self::COL_ALZ_SEARCH_WORD_ID, $id);
		return $this->doUpdate();
	}

	public function getActiveWord($limit)
	{
		$this->addSelect(self::COL_SEARCH_WORD);
		$this->addSelect(self::COL_SEARCH_OPT);
		$this->addWhere(self::COL_SEARCH_TOTAL, 0, '>');
		$this->addOrder(self::COL_SEARCH_TOTAL, 'DESC');
		$this->addLimit($limit);
		return $this->select();
	}
}
?>