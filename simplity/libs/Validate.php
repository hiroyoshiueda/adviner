<?php

define('SP_VALIDATE_NUM', '^[0-9]*$');
define('SP_VALIDATE_SPACE', '\s');
define('SP_VALIDATE_ALPHA_LOWER', '^[a-z]*$');
define('SP_VALIDATE_ALPHA_UPPER', '^[A-Z]*$');
define('SP_VALIDATE_ALPHA', SP_VALIDATE_ALPHA_LOWER . SP_VALIDATE_ALPHA_UPPER);
define('SP_VALIDATE_DATE', '^([0-9]{4})[-/ \.]([01]?[0-9])[-/ \.]([0123]?[0-9])$');
define('SP_VALIDATE_PUNCTUATION',  SP_VALIDATE_SPACE . '\.,;\:&"\'\?\!\(\)');
define('SP_VALIDATE_PATH', '^[a-zA-Z0-9\.\-\/_]+$');
define('SP_VALIDATE_ASCII',  '^[a-zA-Z0-9\.\,;@#\|\%\$=\/\\\:_\*&"\'\?\!\(\)-^~\+\{\}\`]*$');
//define('SP_VALIDATE_ASCII',  '^[a-zA-Z0-9\.\,;\[\]\|@#\%\$=\/\\\:_\*&"\'\?\!\(\)-^~\+]*$');
//define('SP_VALIDATE_PASSWORD', '^[a-zA-Z0-9\.@#\%\$=_\*\&\+\-]+$');
define('SP_VALIDATE_PASSWORD', '^[a-zA-Z0-9\.@#\%\$=_\*\&\-]+$');
//define('SP_VALIDATE_EMAIL', '^[a-zA-Z0-9]+[a-zA-Z0-9\-\._]*@[0-9a-zA-Z\-\._]+\.[a-zA-Z]+$');
define('SP_VALIDATE_EMAIL', '^[-A-Za-z0-9_]+[-A-Za-z0-9_.]*[@]{1}[-A-Za-z0-9_]+[-A-Za-z0-9_.]*[.]{1}[A-Za-z]{2,5}$');
define('SP_VALIDATE_PHONE', '^[0-9]{2,4}\-[0-9]{2,4}\-[0-9]{4}$');
define('SP_VALIDATE_ZIP', '^[0-9]{3}\-[0-9]{4}$');
define('SP_VALIDATE_IP',  '^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$');
define('SP_VALIDATE_IP_PORT',  '^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\:([0-9]{1,4})$');
define('SP_VALIDATE_IP_GROUP',  '^([0-9]{1,3}|[0-9]{0,2}\*|[0-9]{1,3}\-[0-9]{1,3})\.' .
								'([0-9]{1,3}|[0-9]{0,2}\*|[0-9]{1,3}\-[0-9]{1,3})\.' .
								'([0-9]{1,3}|[0-9]{0,2}\*|[0-9]{1,3}\-[0-9]{1,3})\.' .
								'([0-9]{1,3}|[0-9]{0,2}\*|[0-9]{1,3}\-[0-9]{1,3})$');


/**
 *
 * @author Hiroyoshi
 */
class Validate
{
	public static function isEmpty($value)
	{
		if ($value === null || (string)$value == '') return true;
        return false;
	}

	public static function required($value, $opt)
	{
		if (self::isEmpty($value)) return false;
		if (trim($value) == '') return false;
        return true;
	}

	public static function email($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		return ereg(SP_VALIDATE_EMAIL, $value);
	}

	public static function phone($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		return ereg(SP_VALIDATE_PHONE, $value);
	}

	public static function zip($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		return ereg(SP_VALIDATE_ZIP, $value);
	}

	public static function date($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		return ereg(SP_VALIDATE_DATE, $value);
	}

	public static function checkdate($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		$d = preg_split("/[\/\- :]+/", $value);
		return checkdate((int)$d[1], (int)$d[2], (int)$d[0]);
	}

	public static function month_day($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		$d = preg_split("/[\/\-]+/", $value);
		if (self::month($d[0], null) && self::day($d[1], null)) return true;
		return false;
	}

	public static function day($value, $opt)
	{
		if (!self::number($value, null)) return false;
		if ($value < 1 || $value > 31) return false;
		return true;
	}

	public static function month($value, $opt)
	{
        if (!self::number($value, null)) return false;
		if ($value < 1 || $value > 12) return false;
		return true;
	}

	public static function ipport($value, $opt)
	{
		if (self::isEmpty($value)) return false;
		return ereg(SP_VALIDATE_IP_PORT, $value);
	}

	public static function ipgroup($value, $opt)
	{
		if (self::isEmpty($value)) return false;
		return ereg(SP_VALIDATE_IP_GROUP, $value);
	}

	public static function number($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		return ereg(SP_VALIDATE_NUM, $value);
	}

	public static function alpha($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		return ereg(SP_VALIDATE_ALPHA, $value);
	}

	public static function ereg($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt == "") return true;
		return ereg($opt[0] , $value);
	}
	public static function match($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt == "") return true;
		return ereg($opt[0] , $value);
	}

	public static function ascii($value, $opt)
	{
        if (self::isEmpty($value)) return true;
        return ereg(SP_VALIDATE_ASCII, $value);
	}

	public static function password($value, $opt)
	{
        if (self::isEmpty($value)) return true;
        return ereg(SP_VALIDATE_PASSWORD, $value);
	}

	public static function num_range($value, $opt)
	{
		if (self::isEmpty($value)) return true;
        if (!self::number($value, null)) return false;
		if ($value >= $opt[0] && $value <= $opt[1]){
			return true;
		}
		return false;
	}

	public static function num_max($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt == "") return true;
		if ($value > $opt[0]) return false;
		return true;
	}

	public static function num_min($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt == "") return true;
		if ($value > $opt[0]) return false;
		return true;
	}

	public static function urlchar($value,$opt){
        if (self::isEmpty($value))
            return true;
		if ($opt == "")
			return true;

		for ($i = 0; strlen($value) > $i ; $i++) {

			if($value[$i] != ',' || $value[$i] != "\r\n"){
				$pos = strpos($opt[0], $value[$i]);
				if($pos === false){
					return false;
				}
			}
		}
		return true;
	}

	public static function forbid($value, $opt)
	{
        if (self::isEmpty($value)) return true;
		if ($opt[0] == '') return true;
		if (strlen($value) != strcspn($value,$opt[0])) return false;
		return true;
	}

	public static function minlength($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt[0] == '') return true;
		if (strlen($value) < $opt[0]) return false;
		return true;
	}

	public static function maxlength($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt[0] == '') return true;
		if (strlen($value) > $opt[0]) return false;
		return true;
	}

	public static function length_range($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt[0] == '' || $opt[1] == '') return true;
		if (strlen($value)>=$opt[0] && strlen($value)<=$opt[1]) {
			return true;
		}
		return false;
	}

	public static function minlengthZen($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt == "") return true;
		if (mb_strlen($value) < $opt[0]) return false;
		return true;
	}

	public static function maxlengthZen($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt == "") return true;
		if (mb_strlen($value) > $opt[0]) return false;
		return true;
	}

	public static function length_range_zen($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt[0] == '' || $opt[1] == '') return true;
		if (mb_strlen($value)>=$opt[0] && mb_strlen($value)<=$opt[1]) {
			return true;
		}
		return false;
	}

	public static function url($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt[0] === true) {
			if (preg_match("|^https?://.+|i", $value)) return true;
		} else {
			if (preg_match("|^http://.+|i", $value)) return true;
		}
		return false;
	}

	public static function path($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if (preg_match("|^/.+|i", $value)) return true;
		return false;
	}

	public static function max($value, $opt)
	{
		if (self::isEmpty($value)) return true;
		if ($opt == "") return true;
		if ((int)$value > (int)$opt[0]) return false;
		return true;
	}
}

?>