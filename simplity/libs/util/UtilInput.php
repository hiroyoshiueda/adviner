<?php
//class UtilInput {
//	private $output = '';
//	private $internal = '';
//	private $isConvert = false;
//	public static function main($target) {
//		$input = new UtilInput();
//		if (strpos($target, 'G') !== false) array_walk($_GET, array($input, 'filter'), true);
//		if (strpos($target, 'P') !== false) array_walk($_POST, array($input, 'filter'), true);
//	    if (strpos($target, 'C') !== false) array_walk($_COOKIE, array($input, 'filter'), false);
//	    if (strpos($target, 'R') !== false) array_walk($_REQUEST, array($input, 'filter'), true);
//	}
//	function UtilInput() {
//		$this->output = mb_http_output();
//		$this->internal = mb_internal_encoding();
//		if ($this->output != $this->internal) $this->isConvert = true;
//	}
//	public function filter(&$value, $key, $convert) {
//		if (is_array($value)) {
//			array_walk($value, array($this, 'filter'), $convert);
//		} else {
//			$value = $this->safe($value);
//			if (get_magic_quotes_gpc()) $value = stripslashes($value);
//			if ($convert && $this->isConvert) $value = $this->convertEncoding($value);
//		}
//		return;
//	}
//	private function safe($value) {
//		$value = str_replace(array('\0'), array(''), $value);
//		$value = rtrim($value);
//		return $value;
//	}
//	private function convertEncoding($value) {
//		if (strlen($value) == 0) return $value;
//		return mb_convert_encoding($value, $this->internal, $this->output);
//	}
//}
?>