<?php
//class DataSet {
//	private $_result = null;
//	private $_rows = 0;
//	private $_rowSet = array();
//	private $_clientCode = '';
//	function DataSet($result) {
//		$this->_result = $result;
//		$this->_rows = $this->_result->num_rows;
//		$this->_clientCode = mb_internal_encoding();
//	}
//	public function getRowCount() {
//		return $this->_rows;
//	}
//	public function beforeFirst() {
//		mysql_field_seek($this->_result, 0);
//	}
//	public function next() {
//		$this->_rowSet = mysql_fetch_array($this->_result, MYSQL_ASSOC);
//		if ($this->_rowSet === false) {
//			$this->_rowSet = array();
//			return false;
//		}
//		return true;
//	}
//	public function first() {
//		$this->beforeFirst();
//		return $this->next();
//	}
//	public function close() {
//		$this->_result->free();
//		$this->_result = null;
//		$this->_rows = 0;
//		$this->_rowSet = array();
//	}
//	public function getRowSet() {
//		return $this->_rowSet;
//	}
//	public function getString($col) {
//		if ($this->_rowSet[$col] === null) return null;
//		$this->_rowSet[$col] = $this->_convertEncoding($this->_rowSet[$col]);
//		return strval($this->_rowSet[$col]);
//	}
//	public function getInt($col) {
//		if ($this->_rowSet[$col] === null) return null;
//		return empty($this->_rowSet[$col]) ? 0 : intval($this->_rowSet[$col]);
//	}
//	public function getFloat($col) {
//		if ($this->_rowSet[$col] === null) return null;
//		return empty($this->_rowSet[$col]) ? 0 : floatval($this->_rowSet[$col]);
//	}
//	public function getBoolean($col) {
//		if ($this->_rowSet[$col] === null) return null;
//		if ($this->_rowSet[$col]{0} == 't' ||
//				$this->_rowSet[$col]{0} == 'T') {
//			$this->_rowSet[$col] = true;
//		} else if ($this->_rowSet[$col]{0} == 'f' ||
//				$this->_rowSet[$col]{0} == 'F') {
//			$this->_rowSet[$col] = false;
//		}
//		$res = empty($this->_rowSet[$col]) ? false : true;
//		return $res;
//	}
//	public function getTimestamp($col) {
//		if ($this->_rowSet[$col] === null) return null;
//		return $this->_getTimestamp($this->_rowSet[$col]);
//	}
//	public function getDate($col, $format='Y-m-d') {
//		if ($this->_rowSet[$col] === null) return null;
//		$time = $this->_getTimestamp($this->_rowSet[$col]);
//		return date($format, $time);
//	}
//	public function getDatetime($col, $format='Y-m-d h:i:s') {
//		if ($this->_rowSet[$col] === null) return null;
//		$time = $this->_getTimestamp($this->_rowSet[$col]);
//		return date($format, $time);
//	}
//	private function _getTimestamp($dateStr) {
//		if ($dateStr == '') return 0;
//		$time = strtotime($dateStr);
//		if ($time == -1 || $time === false) {
//			// strtotime() was not able to parse $string, use "now":
//			$time = 0;
//		}
//		return $time;
//	}
//	private function _convertEncoding($str) {
//		if ($str == '') return $str;
//		return mb_convert_encoding($str, $this->_clientCode);
//	}
//}
?>