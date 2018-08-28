<?php
function smarty_modifier_str_cut($str, $length)
{
	if (mb_strlen($str) > $length) {
		return mb_substr($str, 0, $length) . '...';
	}
	return $str;
}
?>