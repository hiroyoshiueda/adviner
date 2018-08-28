<?php
function smarty_modifier_datef($str, $format)
{
	Util::import('UtilDate');
	if ($str == '') return '';
	$t = UtilDate::getTimestamp($str);
	$str = UtilDate::format($t, $format);
    return $str;
}
?>