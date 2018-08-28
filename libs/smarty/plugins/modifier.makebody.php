<?php
function smarty_modifier_makebody($string, $is_makelink=true, $is_br=true)
{
	if (empty($string)) return $string;
	$string = htmlspecialchars($string, ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
	if ($is_makelink) $string = app_create_link($string);
	if ($is_br) $string = nl2br($string);
	return $string;
}
?>