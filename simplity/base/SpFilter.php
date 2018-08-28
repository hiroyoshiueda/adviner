<?php
class SpFilter
{
	public static function execute(&$array, $filter='sanitize')
	{
		array_walk($array, array('SpFilter', '__baseCallback'), $filter);
	}
	public static function __baseCallback(&$value, $key, $filter)
	{
		if (is_array($value)) {
			array_walk($value, array('SpFilter', '__baseCallback'), $filter);
		} else {
			$value = SpFilter::$filter($value);
			$value = mb_convert_encoding($value, 'UTF-8', 'ASCII,JIS,UTF-8,sjis-win,eucjp-win');
		}
	}
	public static function sanitize($value)
	{
		if ($value === null || $value == '') return $value;
		$value = str_replace("\0", '', $value);
		$value = rtrim($value);
//		$value = rtrim($value, '　');
		if (get_magic_quotes_gpc()) $value = stripslashes($value);
//		$value = urldecode($value);
		return $value;
	}
	public static function uniform($value, $opt='aKV')
	{
		if ($value === null || $value == '' || $opt == '') return $value;

		if (is_array($value) && count($value)>0) {
			foreach ($value as $k => $v) {
				$value[$k] = self::uniform($v, $opt);
			}
			return $value;
		}

		$opts = explode('|', $opt);
		foreach ($opts as $o) {
			if ($o == 'int') {
				$value = intval($value);
			} else if ($o == 'float') {
				$value = floatval($value);
			} else if ($o == 'lower') {
				$value = strtolower($value);
			} else if ($o == 'upper') {
				$value = strtoupper($value);
			} else if ($o == 'num') {
				$value = str_replace(',', '', $value);
				$value = (strpos($value, '.')===false) ? intval($value) : floatval($value);
			} else if ($o == 'md5') {
				preg_match("/^([0-9a-zA-Z]{32})/", $value, $m);
				$value = $m[1];
			} else if ($o != '') {
				$value = mb_convert_kana($value, $o);
			}
		}
		return $value;
	}
	public static function encoding(&$array, $to, $from)
	{
		if (!is_array($array) || count($array)==0) return;
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				self::encoding($v, $to, $from);
			} else {
				$v = mb_convert_encoding($v, $to, $from);
			}
			$array[$k] = $v;
		}
		return;
	}
}
?>