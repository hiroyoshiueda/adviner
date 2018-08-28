<?php
function smarty_modifier_to_object($arr)
{
	return Util::jsonEncode($arr);
//	return htmlspecialchars(Util::jsonEncode($arr), ENT_QUOTES);
//	$buf  = '';
//	if (is_array($arr) && count($arr)>0) {
//		foreach ($arr as $key => $val) {
//			if ($buf!='') $buf .= ",";
//			$buf .= $key . ":'" . htmlspecialchars($val, ENT_QUOTES) . "'";
//		}
//	}
//	return '{'.$buf.'}';
}
?>