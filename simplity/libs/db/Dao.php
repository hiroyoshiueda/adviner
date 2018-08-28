<?php
/**
 * Enter description here...
 */
class Dao
{
	/**
	 * @var DbManager
	 */
	protected $_db;
	protected $_table = '';
	private $_tablesMap = array();
	private $_tableJoins = array();
	private $_cols = array();
	private $_colSet = array();
	private $_valSet = array();
	private $_wheres = array();
	private $_groups = array();
	private $_orders = array();
	private $_havings = array();
	private $_limit = '';
	private $_offset = '';
	private $_lowPriority = false;
	private $_options = array();

	const COL_BLANK = "''";
	const DATE_NOW = "now()";

	/**
	 *
	 * @param DbManager $db
	 * @param string $table
	 * @param array $options
	 */
	function __construct(&$db, $table, $options=array())
	{
		$this->_db =& $db;
		$this->_table = $table;
		$this->_options = $options;
//		try {
//			$this->_db->connect();
//		} catch(DbException $e) {
//			if ($this->_options['error'] != 'hidden') {
//				throw $e;
//			}
//		}
		$this->reset();
	}
	public function reset() {
		$this->_tablesMap = array();
		$this->_tablesMap[$this->_table] = $this->_table;
		$this->_tableJoins = array();
		$this->_cols = array();
		$this->_colSet = array();
		$this->_valSet = array();
		$this->_wheres = array();
		$this->_groups = array();
		$this->_orders = array();
		$this->_havings = array();
		$this->_limit = '';
		$this->_offset = '';
		$this->_lowPriority = false;
	}
	public function addSelect($col) {
		$this->_cols[] = $col;
	}
	public function addSelectAs($col, $name) {
		$this->_cols[] = $col . ' AS ' . $name;
	}
	public function addSelectAll() {
		$this->_cols[] = '*';
	}
	public function addSelectTable($table, $col, $name='') {
		$newCol = $table . '.' . $col;
		if ($name == '') {
			$this->addSelect($newCol);
		} else {
			$this->addSelectAs($newCol, $name);
		}
	}
	public function addSelectCount($col, $name) {
		$this->addSelectAs('COUNT(' . $col . ')', $name);
	}
	public function addSelectSum($col, $name) {
		$this->addSelectAs('SUM(' . $col . ')', $name);
	}
	public function addSelectMax($col, $name) {
		$this->addSelectAs('MAX(' . $col . ')', $name);
	}
	public function addWhere($col, $val, $comp='=', $cond=null) {
		if ($col==='') $this->_addWhere($val, $cond);
		else $this->_addWhere($col . $comp . $this->_toNumber($val), $cond);
	}
	public function addWhereStr($col, $val, $comp='=', $cond=null) {
		if ($val === null) {
			$comp = ($comp == '=') ? ' IS ' : ' IS NOT ';
			$this->_addWhere($col . $comp . 'NULL', $cond);
		} else {
			$this->_addWhere($col . $comp . $this->_quoteString($val), $cond);
		}
	}
	public function addWhereLike($col, $val, $cond=null) {
		if ($val === null || $val == '') return;
		$this->_addWhere($col . ' LIKE ' . $this->_quoteString($val), $cond);
	}
	public function addFreeSearch($col, $words, $cond='OR', $f='%', $r='%')
	{
		$arr = array();
		foreach ($words as $val) {
			if ($val == '') continue;
			$arr[] = $col.' LIKE '.$this->_quoteString($f.$val.$r);
		}
		if (count($arr) == 0) return;
		$this->_addWhere('('.implode(' '.$cond.' ', $arr).')', 'AND');
	}
	public function addWhereIn($col, $val, $cond=null) {
		if (is_array($val)) $val = implode(',', $val);
		if ($val === null || $val == '') return;
		$this->_addWhere($col . ' IN (' . $val . ')', $cond);
	}
	public function addWhereNotIn($col, $val, $cond=null) {
		if (is_array($val)) $val = implode(',', $val);
		if ($val === null || $val == '') return;
		$this->_addWhere($col . ' NOT IN (' . $val . ')', $cond);
	}
	public function addWhereStrIn($col, $val, $cond=null) {
		if (is_array($val)) {
			$arr = array();
			foreach ($val as $str) {
				if ($str!='') $arr[] = $this->_quoteString($str);
			}
			$val = implode(',', $arr);
		}
		if ($val === null || $val == '') return;
		$this->_addWhere($col . ' IN (' . $val . ')', $cond);
	}
	public function addWhereStrNotIn($col, $val, $cond=null) {
		if (is_array($val)) {
			$arr = array();
			foreach ($val as $str) {
				if ($str!='') $arr[] = $this->_quoteString($str);
			}
			$val = implode(',', $arr);
		}
		if ($val === null || $val == '') return;
		$this->_addWhere($col . ' NOT IN (' . $val . ')', $cond);
	}
	public function addWhereTableJoin($table1, $col1, $table2, $col2) {
		$this->_addWhere($table1.'.'.$col1.'='.$table2.'.'.$col2);
	}
	protected function _addWhere($where, $cond) {
		if ($cond === null) $cond = 'AND';
		if (count($this->_wheres) == 0) {
			$this->_wheres[] = $where;
		} else {
			$this->_wheres[] = ' '.$cond.' '.$where;
		}
	}
	public function addValue($col, $val) {
		if ($val === null) $val = "NULL";
		if ($val == '') $val = 0;
		$this->_colSet[] = $col;
		$this->_valSet[] = $val;
	}
	public function addValueStr($col, $val) {
		$this->_colSet[] = $col;
		$this->_valSet[] = $this->_quoteString($val);
	}
	public function setTable($table, $alias='') {
		if ($alias!='') $alias = ' '.$alias;
		$this->_tablesMap[$table] = $table.$alias;
		return true;
	}
	public function addTable($table, $alias='') {
		if (isset($this->_tablesMap[$table])) return false;
		return $this->setTable($table, $alias);
	}
	public function addTableJoin($table, $alias='', $where='', $join='LEFT OUTER') {
		if ($alias!='') $alias = ' '.$alias;
		$this->_tableJoins[] = $join.' JOIN '.$table.$alias.' ON ('.$where.')';
	}
	public function addGroupBy($col) {
		$this->_groups[] = $col;
	}
	public function addHaving($having) {
		$this->_havings[] = $having;
	}
	public function addOrder($col, $order='') {
		if ($order!='') $order = ' '.$order;
		$this->_orders[] = $col.$order;
	}
	public function addOrderTalbe($table, $col, $order='') {
		$this->addOrder($table.'.'.$col, $order);
	}
	public function addLimit($limit) {
		$this->_limit = $limit;
	}
	public function setLimitOffset($limit, $offset) {
		$this->_limit = $limit;
		$this->_offset = $offset;
	}
	public function getTable() {
		return $this->_table;
	}
	public function getSelect() {
		return $this->_getSelectStatement();
	}
	public function getQuery() {
		return $this->_db->getLastQuery();
	}
	public function quoteString($str) {
		return $this->_quoteString($str);
	}
	public function doSelect() {
		$sql = $this->_getSelectStatement();
		try {
			return $this->_db->executeQuery($sql);
		} catch(DbException $e) {
			if ($this->_options['error'] != 'hidden') {
				throw $e;
			} else {
				return null;
			}
		}
	}
	public function doInsert() {
		$sql = $this->_getInsertStatement();
		if ($this->_db->executeUpdate($sql)) {
			return $this->_db->getUpdateLines();
		}
		return -1;
	}
	public function doUpdate() {
		$sql = $this->_getUpdateStatement();
		if ($this->_db->executeUpdate($sql)) {
			return $this->_db->getUpdateLines();
		}
		return -1;
	}
	public function doDelete() {
		$sql = $this->_getDeleteStatement();
		if ($this->_db->executeUpdate($sql)) {
			return $this->_db->getUpdateLines();
		}
		return -1;
	}
	public function getLastInsertId() {
		$id = 0;
		try {
			// 小文字selectでマスタから
			$id = $this->_db->getInt('select LAST_INSERT_ID()');
		} catch (DbException $e) {
			throw $e;
		}
		return $id;
	}
	public function addColumn($colname,$dtype) {
		$ret = false;
		try {
			$ret = $this->_db->addColumn($this->_table,$colname,$dtype);
		} catch (DbException $e) {
			throw $e;
		}
		return $ret;
	}
	public function delColumn($colname) {
		$ret = false;
		try {
			$ret = $this->_db->delColumn($this->_table,$colname);
		} catch (DbException $e) {
			throw $e;
		}
		return $ret;
	}
	public function getTables($like=null) {
		return $this->_db->getTables($like);
	}
	public function getColumns($like=null) {
		return $this->_db->getColumns($this->_table,$like);
	}
	public function setLowPriority() {
		$this->_lowPriority = true;
	}
//	public function begin() {
//		return $this->_db->beginTransaction();
//	}
//	public function commit() {
//		return $this->_db->commit();
//	}
//	public function rollback() {
//		return $this->_db->rollback();
//	}
	protected function _getSelectStatement() {
		$sql = 'SELECT ';
		if (count($this->_cols) == 0) {
			$sql .= '*';
		} else {
			$sql .= implode(',', $this->_cols);
		}
		$sql .= ' FROM ';
		if (count($this->_tableJoins) == 0) {
			$sql .= implode(',', $this->_tablesMap);
		} else {
			$sql .= current($this->_tablesMap).' ';
			$sql .= implode(' ', $this->_tableJoins);
		}
		$sql .= $this->_getWhereStatement();
		if (count($this->_groups) > 0) {
			$sql .= ' GROUP BY ' . implode(',', $this->_groups);
		}
		if (count($this->_havings) > 0) {
			$sql .= ' HAVING ' . implode(' ', $this->_havings);
		}
		if (count($this->_orders) > 0) {
			$sql .= ' ORDER BY ' . implode(',', $this->_orders);
		}
		if ($this->_limit != '') {
			if ($this->_offset != '' && $this->_offset > 0) {
				$sql .= sprintf(' LIMIT %d,%d', $this->_offset, $this->_limit);
			} else {
				$sql .= ' LIMIT ' . $this->_limit;
			}
		}
		return $sql;
	}
	private function _getInsertStatement() {
		$sql = 'INSERT ';
		if ($this->_lowPriority) $sql .= 'LOW_PRIORITY ';
		$sql .= 'INTO '.$this->_table.' (';
		$sql .= implode(',', $this->_colSet);
		$sql .= ') VALUES (';
		$sql .= implode(',', $this->_valSet);
		$sql .= ')';
		return $sql;
	}
	private function _getUpdateStatement() {
		$sql = 'UPDATE ';
		if ($this->_lowPriority) $sql .= 'LOW_PRIORITY ';
		$sql .= $this->_table.' SET ';
		$len = count($this->_colSet);
		for ($i=0; $i<$len; $i++) {
			if ($i > 0) $sql .= ',';
			$sql .= $this->_colSet[$i].'='.$this->_valSet[$i];
		}
		$sql .= $this->_getWhereStatement();
		return $sql;
	}
	private function _getDeleteStatement() {
		$sql = 'DELETE ';
		if ($this->_lowPriority) $sql .= 'LOW_PRIORITY ';
		$sql .= 'FROM '.$this->_table;
		$sql .= $this->_getWhereStatement();
		return $sql;
	}
	private function _getWhereStatement() {
		if (count($this->_wheres) == 0) return '';
		return ' WHERE ' . implode('', $this->_wheres);
	}
	private function _quoteString($str) {
		if ($str === null) return "NULL";
		$str = $this->_db->quote($str);
		return "'" . $str . "'";
	}
	private function _toNumber($val) {
		if (preg_match("/^(\-?[0-9\.]+)/", $val, $matches)) {
			return $matches[1];
		}
		return null;
	}
	/**
	 * SELECT結果の全てを返す
	 * @return array 配列、連想配列
	 */
	public function select()
	{
		$rows = array();
		$result = $this->doSelect();
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		return $rows;
	}

	/**
	 * SELECT結果の一行を返す
	 * @return array 連想配列
	 */
	public function selectRow()
	{
		$result = $this->doSelect();
		$rows = $result->fetch_assoc();
		if ($rows === null) $rows = array();
		return $rows;
	}

	/**
	 * SELECT結果から指定の行を返す
	 * @param int $offset 開始行数
	 * @param int $limit 取得行数
	 * @param int $total 全件数（参照渡し用）
	 * @return array 結果配列、連想配列
	 */
	public function selectPage($offset, $limit, &$total)
	{
		$total = 0;
		$sql = $this->_getSelectStatement();
		$count_sql = preg_replace("/^SELECT .* FROM /i", 'SELECT COUNT(*) as cnt FROM ', $sql);
		try {
			$total = $this->_db->getInt($count_sql);
		} catch (DbException $e) {
			throw $e;
		}
		$offset_limit = sprintf('%d,%d', $offset, $limit);
		$this->addLimit($offset_limit);
		$sql .= ' LIMIT '.$offset_limit;
		$rows = array();
		if ($total == 0) {
			return $rows;
		}
		try {
			$result = $this->_db->executeQuery($sql);
		} catch(DbException $e) {
			if ($this->_options['error'] != 'hidden') {
				throw $e;
			} else {
				return null;
			}
		}
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		return $rows;
	}

	/**
	 * SELECT結果の先頭1つ目を数値で返す
	 * @return int
	 */
	public function selectId()
	{
		$sql = $this->_getSelectStatement();
		return $this->_db->getInt($sql);
	}

	public function selectOne()
	{
		$sql = $this->_getSelectStatement();
		return $this->_db->getString($sql);
	}

	public function selectKeySet($key)
	{
		$rows = array();
		$result = $this->doSelect();
		while ($row = $result->fetch_assoc()) {
			if ($row[$key]=='') continue;
			$rows[$row[$key]] = $row;
		}
		return $rows;
	}

	public function selectKeyValue($key_col, $value_col)
	{
		$rows = array();
		$result = $this->doSelect();
		while ($row = $result->fetch_assoc()) {
			if ($row[$key_col]=='') continue;
			$rows[$row[$key_col]] = $row[$value_col];
		}
		return $rows;
	}

	public function selectOneArray($col)
	{
		$rows = array();
		$result = $this->doSelect();
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row[$col];
		}
		return $rows;
	}

	public function getSelectId($sel, $where=null)
	{
		$sql = $sel . ' FROM ' . $this->_table;
		if ($where!==null) $sql .= ' WHERE ' . $where;
		return $this->_db->getInt($sql);
	}

	public function getSelectOne($sel, $where=null)
	{
		$sql = $sel . ' FROM ' . $this->_table;
		if ($where!==null) $sql .= ' WHERE ' . $where;
		return $this->_db->getString($sql);
	}
}
?>