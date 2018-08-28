<?php
function smarty_modifier_form_set_license($license, $app_const_license)
{
	$str = '';
	if (is_array($license)) {
		foreach ($license as $txt) {
			if (in_array($txt, $app_const_license)===false) $str .= ($str=='') ? $txt : "\n".$txt;
		}
	}
    return $str;
}
?>