<?php
/**
 * 環境設定
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class EnvsDao extends BaseDao
{
	const TABLE_NAME = 'envs';

	const COL_ENV_ID = "env_id";
	const COL_ENV_TYPE = "env_type";
	const COL_ENV_KEY = "env_key";
	const COL_ENV_VALUE = "env_value";

	const ENV_TYPE_USER = 0;
	const ENV_TYPE_SYSTEM = 1;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList()
	{
		$this->addWhere(self::COL_ENV_TYPE, self::ENV_TYPE_USER);
		return $this->select();
	}

	public function getItem($key)
	{
		$this->addWhereStr(self::COL_ENV_KEY, $key);
		$this->addWhere(self::COL_ENV_TYPE, self::ENV_TYPE_USER);
		return $this->selectRow();
	}

	public function delete($key)
	{
		$this->reset();
		$this->addValueStr(self::COL_ENV_VALUE, '');
		$this->addWhereStr(self::COL_ENV_KEY, $key);
		$this->addWhere(self::COL_ENV_TYPE, self::ENV_TYPE_USER);
		return $this->doUpdate();
	}

	public function getKeyValue()
	{
		$this->addSelect(self::COL_ENV_KEY);
		$this->addSelect(self::COL_ENV_VALUE);
		$this->addWhere(self::COL_ENV_TYPE, self::ENV_TYPE_USER);
		return $this->selectKeyValue(self::COL_ENV_KEY, self::COL_ENV_VALUE);
	}

	public function save($key, $value)
	{
		$this->reset();
		$this->addValueStr(self::COL_ENV_VALUE, $value);
		$this->addWhereStr(self::COL_ENV_KEY, $key);
		$this->addWhere(self::COL_ENV_TYPE, self::ENV_TYPE_USER);
		return $this->doUpdate();
	}
}
?>